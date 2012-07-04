<?php
class Admin_View_Helper_LoggedInAs extends Zend_View_Helper_Abstract 
{
    public function loggedInAs ($baseUrl)
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $username = $auth->getIdentity()->user_name;
            $logoutUrl = $this->view->url(array('module'=>'admin','controller'=>'index',
                'action'=>'logout'), null, true);
            return '<span>'.$username .  '</span> <a href="'.$logoutUrl.'"><img src="'.$baseUrl.'/images/shared/nav/nav_logout.gif" width="84" height="14" alt="" align="right" /></a>';
        } 

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if($controller == 'index' && $action == 'index') {
            return '';
        }
        $loginUrl = $this->view->url(array('module'=>'admin', 'controller'=>'index', 'action'=>'index'));
        return '<a href="'.$loginUrl.'">Login</a>';
    }
    //<img src="$this->baseUrl() .'/images/shared/nav/nav_logout.gif" width="64" height="14" alt="" /></a>
}

