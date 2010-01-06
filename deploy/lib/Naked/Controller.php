<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked;

use Naked\Template\Loader as TemplateLoader;
use Naked\Response;
use Naked\Response\Redirect;

/**
 * The base controller
 *
 * @package Naked
 * @author Matthew Purdon
 */
class Controller
{
    /**
     * @var Naked\Request
     */
    protected $request;

    /**
     * @var Naked\Application\Environment
     */
    protected $environment;

    /**
     * @var Naked\Configuration
     */
    protected $configuration;

    /**
     * Constructor
     *
     * @Inject
     * @param Naked\Request $request
     * @param Naked\Application\Environment $environment
     */
    public function __construct(\Naked\Request $request,
                                \Naked\Application\Environment $environment,
                                \Naked\Application\Configuration $configuration)
    {
        $this->request = $request;
        $this->environment = $environment;
        $this->configuration = $configuration;
    }

    /**
     * Render a context
     *
     * @param string $templateFile
     * @param Naked\Template\Context $extraContext
     * @return Naked\Response
     */
    public function directToTemplate($templateFile, $extraContext=array())
    {
        require_once 'Naked\Template.php';
        require_once 'Naked\Template\Loader.php';
        require_once 'Naked\Template\Lexer.php';
        require_once 'Naked\Template\Parser.php';
        require_once 'Naked\Template\Node.php';
        require_once 'Naked\Template\Node\Nodes.php';
        require_once 'Naked\Template\Node\TextNode.php';
        require_once 'Naked\Template\Node\VariableNode.php';
        require_once 'Naked\Template\Node\Variable.php';
        require_once 'Naked\Template\Node\FilterExpression.php';

        $context = $extraContext;
        $template = TemplateLoader::getTemplate($templateFile);

        return new Response($template->render($context));
    }

    /**
     * Render the context as a JSON response
     *
     * @param Naked\Template\Context $extraContext
     * @return Naked\Response
     */
    public function directToJson($extraContext)
    {
        return new Response(json_encode($extraContext));
    }

    /**
     * Redirect to a new URL
     *
     * @param string $url
     * @return Naked\Response\Redirect
     */
    public function redirectTo($url)
    {
        return new Redirect($url);
    }
}
