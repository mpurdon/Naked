 <?php
/**
 * Naked Framework
 *
 * @package    Naked
 * @subpackage Default
 * @author     Matthew Purdon <matthew@codenaked.org>
 * @version    $Id$
 */

/**
 * Controls MODEL_NAME actions
 *
 * @package    Naked
 * @subpackage Default
 */
class CONTROLLER_NAME extends MDJP_Controller_Action
{
    /**
     * Provides index action
     *
     * @return void
     */
    public function indexAction()
    {
        $c = new Context();
        $c->title = 'Index Action';
        $c->breadCrumbs['MODEL_NAME'] = '/MODULE_NAMEMODEL_URL';
        $this->directToTemplate('index.tpl', $c);
    }
}
