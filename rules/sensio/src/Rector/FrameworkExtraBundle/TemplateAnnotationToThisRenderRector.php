<?php

declare(strict_types=1);

namespace Rector\Sensio\Rector\FrameworkExtraBundle;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Type\MixedType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocNode\Sensio\SensioTemplateTagValueNode;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Sensio\NodeFactory\ThisRenderFactory;
use Rector\Sensio\TypeAnalyzer\ArrayUnionResponseTypeAnalyzer;
use Rector\Sensio\TypeDeclaration\ReturnTypeDeclarationUpdater;

/**
 * @see https://github.com/symfony/symfony-docs/pull/12387#discussion_r329551967
 * @see https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/view.html
 * @see https://github.com/sensiolabs/SensioFrameworkExtraBundle/issues/641
 *
 * @see \Rector\Sensio\Tests\Rector\FrameworkExtraBundle\TemplateAnnotationToThisRenderRector\TemplateAnnotationToThisRenderRectorTest
 */
final class TemplateAnnotationToThisRenderRector extends AbstractRector
{
    /**
     * @var string
     */
    private const RESPONSE_CLASS = 'Symfony\Component\HttpFoundation\Response';

    /**
     * @var ReturnTypeDeclarationUpdater
     */
    private $returnTypeDeclarationUpdater;

    /**
     * @var ThisRenderFactory
     */
    private $thisRenderFactory;

    /**
     * @var ArrayUnionResponseTypeAnalyzer
     */
    private $arrayUnionResponseTypeAnalyzer;

    public function __construct(
        ReturnTypeDeclarationUpdater $returnTypeDeclarationUpdater,
        ThisRenderFactory $thisRenderFactory,
        ArrayUnionResponseTypeAnalyzer $arrayUnionResponseTypeAnalyzer
    ) {
        $this->returnTypeDeclarationUpdater = $returnTypeDeclarationUpdater;
        $this->thisRenderFactory = $thisRenderFactory;
        $this->arrayUnionResponseTypeAnalyzer = $arrayUnionResponseTypeAnalyzer;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition(
            'Turns `@Template` annotation to explicit method call in Controller of FrameworkExtraBundle in Symfony',
            [
                new CodeSample(
                    <<<'PHP'
/**
 * @Template()
 */
public function indexAction()
{
}
PHP
                    ,
                    <<<'PHP'
public function indexAction()
{
    return $this->render("index.html.twig");
}
PHP
                ),
            ]
        );
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Class_) {
            return $this->addAbstractControllerParentClassIfMissing($node);
        }

        if ($node instanceof ClassMethod) {
            return $this->replaceTemplateAnnotation($node);
        }

        return null;
    }

    private function addAbstractControllerParentClassIfMissing(Class_ $node): ?Class_
    {
        if ($node->extends !== null) {
            return null;
        }

        if (! $this->classHasTemplateAnnotations($node)) {
            return null;
        }

        $node->extends = new FullyQualified('Symfony\Bundle\FrameworkBundle\Controller\AbstractController');

        return $node;
    }

    private function replaceTemplateAnnotation(ClassMethod $classMethod): ?Node
    {
        if (! $classMethod->isPublic()) {
            return null;
        }

        /** @var SensioTemplateTagValueNode|null $sensioTemplateTagValueNode */
        $sensioTemplateTagValueNode = $this->getPhpDocTagValueNode($classMethod, SensioTemplateTagValueNode::class);
        if ($sensioTemplateTagValueNode === null) {
            return null;
        }

        $this->refactorClassMethod($classMethod, $sensioTemplateTagValueNode);

        if ($this->hasThisRender($classMethod)) {
            $this->removePhpDocTagValueNode($classMethod, SensioTemplateTagValueNode::class);
        }

        return $classMethod;
    }

    private function classHasTemplateAnnotations(Class_ $class): bool
    {
        foreach ($class->getMethods() as $classMethod) {
            /** @var PhpDocInfo|null $phpDocInfo */
            $phpDocInfo = $classMethod->getAttribute(AttributeKey::PHP_DOC_INFO);
            if ($phpDocInfo === null) {
                continue;
            }

            if ($phpDocInfo->hasByType(SensioTemplateTagValueNode::class)) {
                return true;
            }
        }

        return false;
    }

    private function refactorClassMethod(
        ClassMethod $classMethod,
        SensioTemplateTagValueNode $sensioTemplateTagValueNode
    ): void {
        /** @var Return_|null $return */
        $return = $this->betterNodeFinder->findLastInstanceOf((array) $classMethod->stmts, Return_::class);

        if ($return === null) {
            $this->processClassMethodWithoutReturn($classMethod, $sensioTemplateTagValueNode);
        } elseif ($return->expr !== null) {
            // create "$this->render('template.file.twig.html', ['key' => 'value']);" method call
            $thisRenderMethodCall = $this->thisRenderFactory->create(
                $classMethod,
                $return,
                $sensioTemplateTagValueNode
            );

            $returnStaticType = $this->getStaticType($return->expr);

            if (! $return->expr instanceof MethodCall) {
                $return->expr = $thisRenderMethodCall;
            } elseif ($returnStaticType instanceof MixedType) {
                return;
            }

            $isArrayOrResponseType = $this->arrayUnionResponseTypeAnalyzer->isArrayUnionResponseType(
                $returnStaticType,
                self::RESPONSE_CLASS
            );

            if ($isArrayOrResponseType) {
                $this->processIsArrayOrResponseType($return, $return->expr, $thisRenderMethodCall);
            }
        }

        $this->returnTypeDeclarationUpdater->updateClassMethod($classMethod, self::RESPONSE_CLASS);
        $this->removePhpDocTagValueNode($classMethod, SensioTemplateTagValueNode::class);
    }

    private function processClassMethodWithoutReturn(
        ClassMethod $classMethod,
        SensioTemplateTagValueNode $sensioTemplateTagValueNode
    ): void {
        // create "$this->render('template.file.twig.html', ['key' => 'value']);" method call
        $thisRenderMethodCall = $this->thisRenderFactory->create($classMethod, null, $sensioTemplateTagValueNode);
        $classMethod->stmts[] = new Return_($thisRenderMethodCall);
    }

    private function processIsArrayOrResponseType(
        Return_ $return,
        Expr $returnExpr,
        MethodCall $thisRenderMethodCall
    ): void {
        $this->removeNode($return);

        // create instance of Response → return response, or return $this->render
        $responseVariable = new Variable('response');

        $assign = new Assign($responseVariable, $returnExpr);

        $if = new If_(new Instanceof_($responseVariable, new FullyQualified(self::RESPONSE_CLASS)));
        $if->stmts[] = new Return_($responseVariable);

        $returnThisRender = new Return_($thisRenderMethodCall);
        $this->addNodesAfterNode([$assign, $if, $returnThisRender], $return);
    }

    private function hasThisRender(ClassMethod $classMethod): bool
    {
        $hasThisRender = false;

        $this->traverseNodesWithCallable((array) $classMethod->stmts, function (Node $node) use (&$hasThisRender) {
            if (! $this->isLocalMethodCallNamed($node, 'render')) {
                return null;
            }

            $hasThisRender = true;
        });

        return $hasThisRender;
    }
}