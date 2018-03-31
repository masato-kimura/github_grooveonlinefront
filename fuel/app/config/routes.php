<?php
return array(
	'_root_'  => 'index/',  // The default route
	'_404_'   => 'err/404',    // The 404 route
	//'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
	'aboutus'								=> 'index/aboutus/',
	'info/(:any)'									=> 'index/info/$1',
	'login' 								=> 'login/login/index/',
	'login/logout'							=> 'login/login/logout/',
	'login/grooveonlineregistselect'		=> 'login/login/grooveonlineregistselect/',
	'login/grooveonlineregistindex' 		=> 'login/login/grooveonlineregistindex/',
	'login/grooveonlineregistconfirm' 		=> 'login/login/grooveonlineregistconfirm/',
	'login/grooveonlineregistexecute'		=> 'login/login/grooveonlineregistexecute/',
	'login/grooveonlinepassreissuerequest'	=> 'login/login/grooveonlinepassreissuerequest/',
	'login/grooveonlinepassreissuesendmail'	=> 'login/login/grooveonlinepassreissuesendmail/',
	'login/grooveonlinepassreissueform'		=> 'login/login/grooveonlinepassreissueform/',
	'login/grooveonlinepassreissueupdate'	=> 'login/login/grooveonlinepassreissueupdate/',
	'login/editregistindex'		=> 'login/login/editregistindex',
	'login/editregistconfirm' 	=> 'login/login/editregistconfirm/',
	'login/editregistexecute'	=> 'login/login/editregistexecute/',
	'login/grooveonline'		=> 'login/login/grooveonline/',
	'login/editfacebook'		=> 'login/login/editfacebook/',
	'login/facebook'			=> 'login/login/facebook/',
	'login/twitter'				=> 'login/login/twitter/',
	'login/google'				=> 'login/login/google/',
	'login/yahoo'				=> 'login/login/yahoo/',
	'group'						=> 'group/group/index/',
);