<?php 
class Nisanth_ACL_Factory {        
    private static $_sessionNameSpace = 'Nisanth_ACL_Namespace';
    private static $_objAuth;
    private static $_objAclSession;
    private static $_objAcl;
 
    public static function get(Zend_Auth $objAuth,$clearACL=false) {
 
        self::$_objAuth = $objAuth;
	self::$_objAclSession = new Zend_Session_Namespace(self::$_sessionNameSpace);
 
	if($clearACL) {self::_clear();}
 
	    //if(isset(self::$_objAclSession->acl)) {
		//  return self::$_objAclSession->acl;		
	  // } else {
	        return self::_loadAclFromDB();
	  // }
	}
 
    private static function _clear() {
        unset(self::$_objAclSession->acl);
    }
 
    private static function _saveAclToSession() {
        self::$_objAclSession->acl = self::$_objAcl;
    }
 
    private static function _loadAclFromDB() {
	
	$objRoles         = new Nisanth_Model_Roles();
	$objResources     = new Nisanth_Model_Resources();
	$objRoleResources = new Nisanth_Model_Roleresources();
	
        $arrRoles = $objRoles->getAll();
	$arrResources = $objResources->getAll();
	$arrRoleResources = $objRoleResources->getAll();
 
	self::$_objAcl = new Zend_Acl();
 
	self::$_objAcl->addRole(new Zend_Acl_Role('guest'));
	foreach($arrRoles as $role) {
            self::$_objAcl->addRole(new Zend_Acl_Role($role['role']));	
        }
 
        // add all resources to the acl
        foreach($arrResources as $resource) {
            self::$_objAcl->add(new Zend_Acl_Resource($resource['module'] .'::' .$resource['controller'] .'::' .$resource['action']));	
        }
 
        // allow roles to resources
        foreach($arrRoleResources as $roleResource) {
		//	print_r($roleResource);exit;
            self::$_objAcl->allow($roleResource['role'],$roleResource['module'] .'::' .$roleResource['controller'] .'::' .$roleResource['action']);	
        }
 
	self::_saveAclToSession();	
        //************************ ACL Role inheritance ************
         self::$_objAcl->addRole(new Zend_Acl_Role(Entities_RoleTable::getGuestRoleName()));
          while(count($arrRoles) > 0) {
  $role = array_shift($arrRoles);
    if($role['id'] != Entities_RoleTable::getAdminRoleId()) {
      if(isset($role['inherits'])) {
        $exists = true;
	$isCore = false;
	foreach($role['inherits'] as $index => $inherited) {
	  if(in_array($inherited,$arrCoreRoles)) {
	    $isCore = true;
	  } else {
	    unset($role['inherits'][$index]);
	  }
 
	  if(!self::$_objAcl->hasRole($inherited)) {
	    $exists = false;
	  }
	}
 
        if($exists && $isCore) {
	  $role['inherits'][] = Entities_RoleTable::getGuestRoleName();
	  self::$_objAcl->addRole(new Zend_Acl_Role($role['role']),$role['inherits']);
	} else{
	  $arrRoles[] = $role;
	}
      } else {
        self::$_objAcl->addRole(new Zend_Acl_Role($role['role']),array(Entities_RoleTable::getGuestRoleName()));
      }
    }
  }
 
  // add admin account and inherit all roles
  self::$_objAcl->addRole(new Zend_Acl_Role(Entities_RoleTable::getAdminRoleName()),$arrCoreRoles);
  
  
  
  //22222
  // exceptions to the rule - add and remove specific resources per role
  if(self::$_objAuth->hasIdentity()) {
    $arrRole = self::$_objAuth->getIdentity();
    $roleName = $arrRole['role'];
    $userId = $arrRole['id'];
    $accountId = $arrRole['accountId'];
    $arrAllow = Entities_AllowResourceTable::getAllowedResources($accountId,$userId);
    $arrDeny = Entities_DenyResourceTable::getDeniedResources($accountId,$userId);
 
    if(count($arrAllow) > 0) {
      foreach($arrAllow as $resource) {
        self::$_objAcl->allow($roleName,$resource);
      }
    }
 
    if(count($arrDeny) > 0) {
      foreach($arrDeny as $resource) {
        self::$_objAcl->deny($roleName,$resource);
      }
    }
  }
  
  //33333333333
  // Create an array of core roles to check inherited roles against
foreach($arrRoles as $role) {
 if($role['id'] != Entities_RoleTable::getAdminRoleId()) {
   $arrCoreRoles[] = $role['role'];
  }	
}
        //******************* Role inheritance ends **************
	return self::$_objAcl;
        
        
    }
}