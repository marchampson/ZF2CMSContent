<?php

namespace ElmContent;

return array (
		
		'controllers' => array (
				'invokables' => array (
						'ElmContent\Controller\Item' => 'ElmContent\Controller\ItemController',
				        'ElmContent\Controller\Webpage' => 'ElmContent\Controller\WebpageController',
				        'ElmContent\Controller\News' => 'ElmContent\Controller\NewsController',
				        'ElmContent\Controller\Event' => 'ElmContent\Controller\EventController',
				        'ElmContent\Controller\Banner' => 'ElmContent\Controller\BannerController',
						'ElmContent\Controller\Category' => 'ElmContent\Controller\CategoryController',
						'login-index'=>'ElmContent\Controller\LoginController', 
				),
		        'factories' => array(
		                
		        ),
		),
		
		'service_manager' => array (
				'factories' => array (
						'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
				        
				),
				
				'invokables' => array (
						'parentPagesService' => 'ElmContent\Service\ParentPagesService',
						'categoryService' => 'ElmContent\Service\CategoryService',
				        'pagePickerService' => 'ElmContent\Service\PagePickerService'
				) 
		),
		
		'router' => array (
				'routes' => array (
				        'pages-cms' => array (
				                'type' => 'segment',
				                'options' => array (
				                        'route' => '/elements/webpage[/:action][/:id]',
				                        'constraints' => array (
				                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
				                                'id' => '[0-9]*'
				                        ),
				                        'defaults' => array (
				                                'controller' => 'ElmContent\Controller\Webpage',
				                                'action' => 'list'
				                        )
				                )
				        ),
				        'page-functions' => array (
				                'type' => 'segment',
				                'options' => array (
				                        'route' => '/elements/page[/:action][/:id]',
				                        'constraints' => array (
				                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
				                                'id' => '[0-9]*'
				                        ),
				                        'defaults' => array (
				                                'controller' => 'ElmContent\Controller\Webpage',
				                                'action' => 'list'
				                        )
				                )
				        ),
				        'news-cms' => array (
				                'type' => 'segment',
				                'options' => array (
				                        'route' => '/elements/news[/:action][/:id]',
				                        'constraints' => array (
				                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
				                                'id' => '[0-9]*'
				                        ),
				                        'defaults' => array (
				                                'controller' => 'ElmContent\Controller\News',
				                                'action' => 'list'
				                        )
				                )
				        ),
				        'event-cms' => array (
				                'type' => 'segment',
				                'options' => array (
				                        'route' => '/elements/event[/:action][/:id]',
				                        'constraints' => array (
				                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
				                                'id' => '[0-9]*'
				                        ),
				                        'defaults' => array (
				                                'controller' => 'ElmContent\Controller\Event',
				                                'action' => 'list'
				                        )
				                )
				        ),
				        'banner-cms' => array (
				                'type' => 'segment',
				                'options' => array (
				                        'route' => '/elements/banner[/:action][/:id]',
				                        'constraints' => array (
				                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
				                                'id' => '[0-9]*'
				                        ),
				                        'defaults' => array (
				                                'controller' => 'ElmContent\Controller\Banner',
				                                'action' => 'list'
				                        )
				                )
				        ),
						'item' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/elements/content/item[/:action]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'ElmContent\Controller\Item',
												'action' => 'list' 
										) 
								) 
						),
						'page-list' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/elements/page',
										'constraints' => array (
												'namespace' => '[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'ElmContent\Controller\Webpage',
												'action' => 'list' 
										) 
								) 
						),
						'page-add' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/elements/content/item/add[/:namespace]',
										'constraints' => array (
												'namespace' => '[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'ElmContent\Controller\Item',
												'action' => 'add' 
										) 
								) 
						),
						'page-edit' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/elements/content/item/edit[/:namespace][/:id]',
										'constraints' => array (
												'namespace' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[0-9]*' 
										),
										'defaults' => array (
												'controller' => 'ElmContent\Controller\Item',
												'action' => 'edit' 
										) 
								) 
						),
						'page-clone' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/elements/content/item/clone[/:namespace][/:id]',
										'constraints' => array (
												'namespace' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[0-9]*' 
										),
										'defaults' => array (
												'controller' => 'ElmContent\Controller\Item',
												'action' => 'clone' 
										) 
								) 
						),
						'page-confirm-delete' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/elements/content/item/confirm-delete[/:namespace][/:id]',
										'constraints' => array (
												'namespace' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[0-9]*' 
										),
										'defaults' => array (
												'controller' => 'ElmContent\Controller\Item',
												'action' => 'confirm-delete' 
										) 
								) 
						),
						'page-delete' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/elements/page/delete[/:id]',
										'constraints' => array (
												'id' => '[0-9]*' 
										),
										'defaults' => array (
												'controller' => 'ElmContent\Controller\Webpage',
												'action' => 'delete' 
										) 
								) 
						),
						
                        /*
                         * Category
                         */
                        'category' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/elements/category[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[0-9]+' 
										),
										'defaults' => array (
												'controller' => 'ElmContent\Controller\Category',
												'action' => 'list' 
										) 
								) 
						),
				        'form-authentication' => array(
				                'type' => 'Zend\Mvc\Router\Http\Literal',
				                'options' => array(
				                        'route' => '/elements/form/authentication',
				                        'defaults' => array(
				                                'controller' => 'login-index',
				                                'action' => 'form-authentication',
				                        ),
				                ),
				        ),
				        'login-index' => array(
				                'type' => 'Zend\Mvc\Router\Http\Literal',
				                'options' => array(
				                        'route' => '/elements/login',
				                        'defaults' => array(
				                                'controller' => 'login-index',
				                                'action' => 'index',
				                        ),
				                ),
				        ),
				        'login-invalid' => array(
				                'type' => 'Zend\Mvc\Router\Http\Literal',
				                'options' => array(
				                        'route' => '/login-index/invalid',
				                        'defaults' => array(
				                                'controller' => 'login-index',
				                                'action' => 'invalid',
				                        ),
				                ),
				        ),
				        'forgot-password' => array(
				                'type' => 'Zend\Mvc\Router\Http\Literal',
				                'options' => array(
				                        'route' => '/elements/login/forgot-password',
				                        'defaults' => array(
				                                'controller' => 'login-index',
				                                'action' => 'forgot-password',
				                        ),
				                ),
				        ),
				        'change-password' => array(
				                'type' => 'Zend\Mvc\Router\Http\Literal',
				                'options' => array(
				                        'route' => '/elements/login/change-password',
				                        'defaults' => array(
				                                'controller' => 'login-index',
				                                'action' => 'change-password',
				                        ),
				                ),
				        ),
				        'logout' => array(
				                'type' => 'Zend\Mvc\Router\Http\Literal',
				                'options' => array(
				                        'route' => '/elements/logout',
				                        'defaults' => array(
				                                'controller' => 'login-index',
				                                'action' => 'logout',
				                        ),
				                ),
				        ),
				) 
		),
		
		'translator' => array (
				'locale' => 'en_GB',
				'translation_file_patterns' => array (
						array (
								'type' => 'gettext',
								'base_dir' => __DIR__ . '/../language',
								'pattern' => '%s.mo' 
						) 
				) 
		),
		
		'view_manager' => array (
				'display_not_found_reason' => true,
				'display_exceptions' => true,
				'doctype' => 'HTML5',
				'not_found_template' => 'error/404',
				'exception_template' => 'error/index',
				'template_map' => array (
						'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
						'error/404' => __DIR__ . '/../view/error/404.phtml',
						'error/index' => __DIR__ . '/../view/error/index.phtml' 
				),
				'template_path_stack' => array (
						'ElmContent' => __DIR__ . '/../view' 
				),
				
				'strategies' => array (
						'ViewJsonStrategy' 
				) 
		),
		
		'view_helpers' => array (
				'invokables' => array () 
		// 'elmListFilter' => 'ElmContent\View\Helper\ElmListFilter'
		
		),
		
		'asset_manager' => array(
				'resolver_configs' => array(
						'paths' => array(
								'ElmContent' => __DIR__ . '/../public',
						),
				),
		),
        /*
		'QuConfig' => array(
                        'QuCKEditor' => array
                        (
                                'QuBasePath' =>'/js/plugins/ckeditor',
                        ),
                ),
        */
        'QuConfig' => array(
                'QuCKEditor' => array
                (
                        'BasePath' =>'/modules/elm-content/js/plugins/ckeditor',
                        'CKEDITOR' => array(

                            'filebrowserBrowseUrl'    => '/quelfinder/ckeditor',
                            'filebrowserWindowWidth'  => "1000",
                            'filebrowserWindowHeight' => "600",

                            'Width'      => "100%",
                            'Height'     => "340",
                            'language'   => 'en',
                            'uiColor'=> '#F6F6F6',

                            //Producing HTML Compliant Output
                            'coreStyles_bold'   => array('element' => 'b'),
                            'coreStyles_italic' => array('element' => 'i'),
                            'fontSize_style'    => array('element' => 'font','attributes' => array( 'size' => '100px')),

                            //MagiCline plugin
                            'magicline_color'=>'blue',

                            //Full Page Editing
                            'fullPage' => false,
                            'allowedContent' => false,

                            // Toolbars config
                            //'toolbar' => null,
                            //'toolbarGroups' => null,

                            // Full toolbars
                            
                            'toolbar' => array(
                                array('name'=> 'clipboard',
                                    'groups'=> array( 'clipboard', 'undo' ),
                                    'items' => array( 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' )
                                ),
                                array( 'name'=> 'editing',
                                    'groups' => array( 'find', 'selection', 'spellchecker'),
                                    'items' => array( 'Scayt'),
                                ),
                                array( 'name'=> 'links',
                                    'items' => array( 'Link', 'Unlink', 'Anchor' ),
                                ),
                                array( 'name'=> 'insert',
                                    'items' => array( 'Image', 'Table', 'HorizontalRule', 'SpecialChar'),
                                ),
                                array( 'name'=> 'tools',
                                    'items' => array( 'Maximize'),
                                ),
                                array( 'name'=> 'document',
                                    'groups' => array( 'mode', 'document', 'doctools' ), 'items' => array('Source')
                                ),
                                array( 'name'=> 'others',
                                    'items' => array( '-' ),
                                ),
                                '/',
                                array( 'name'=> 'basicstyles',
                                    'groups' => array( 'basicstyles', 'cleanup' ),
                                    'items' => array( 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ),
                                ),
                                array( 'name'=> 'paragraph',
                                    'groups' => array( 'list', 'indent', 'blocks', 'align' ),
                                    'items' => array( 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ),
                                ),
                                array( 'name'=> 'styles',
                                    'items' => array( 'Styles', 'Format' ),
                                ),
                                array( 'name'=> 'about',
                                    'items' => array( 'About' ),
                                ),
                            ),
                            
                            /*
                             // Groups toolbars
                            'toolbarGroups' => array(
                                array( 'name'=> 'clipboard',
                                    'groups'=> array('clipboard', 'undo')
                                ),
                                array( 'name'=> 'editing',
                                    'groups'=> array( 'find', 'selection', 'spellchecker')
                                ),
                                array( 'name'=> 'links'),
                                array( 'name'=> 'insert'),
                                array( 'name'=> 'forms'),
                                array( 'name'=> 'document',
                                    'groups'=> array( 'find', 'selection', 'spellchecker')
                                ),
                                array( 'name'=> 'others'),
                                '/',
                                array( 'name'=> 'document',
                                    'groups'=> array( 'basicstyles', 'cleanup')
                                ),
                                array( 'name'=> 'paragraph',
                                    'groups'=> array( 'list', 'indent', 'blocks', 'align')
                                ),
                                array( 'name'=> 'styles'),
                                array( 'name'=> 'colors'),
                                array( 'name'=> 'about')
                            ),
                            */

                    ),
                    
                ),
        ),
);
