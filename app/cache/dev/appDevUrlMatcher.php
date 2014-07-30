<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * appDevUrlMatcher
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appDevUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($pathinfo);
        $context = $this->context;
        $request = $this->request;

        if (0 === strpos($pathinfo, '/_')) {
            // _wdt
            if (0 === strpos($pathinfo, '/_wdt') && preg_match('#^/_wdt/(?P<token>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => '_wdt')), array (  '_controller' => 'web_profiler.controller.profiler:toolbarAction',));
            }

            if (0 === strpos($pathinfo, '/_profiler')) {
                // _profiler_home
                if (rtrim($pathinfo, '/') === '/_profiler') {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($pathinfo.'/', '_profiler_home');
                    }

                    return array (  '_controller' => 'web_profiler.controller.profiler:homeAction',  '_route' => '_profiler_home',);
                }

                if (0 === strpos($pathinfo, '/_profiler/search')) {
                    // _profiler_search
                    if ($pathinfo === '/_profiler/search') {
                        return array (  '_controller' => 'web_profiler.controller.profiler:searchAction',  '_route' => '_profiler_search',);
                    }

                    // _profiler_search_bar
                    if ($pathinfo === '/_profiler/search_bar') {
                        return array (  '_controller' => 'web_profiler.controller.profiler:searchBarAction',  '_route' => '_profiler_search_bar',);
                    }

                }

                // _profiler_purge
                if ($pathinfo === '/_profiler/purge') {
                    return array (  '_controller' => 'web_profiler.controller.profiler:purgeAction',  '_route' => '_profiler_purge',);
                }

                if (0 === strpos($pathinfo, '/_profiler/i')) {
                    // _profiler_info
                    if (0 === strpos($pathinfo, '/_profiler/info') && preg_match('#^/_profiler/info/(?P<about>[^/]++)$#s', $pathinfo, $matches)) {
                        return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_info')), array (  '_controller' => 'web_profiler.controller.profiler:infoAction',));
                    }

                    // _profiler_import
                    if ($pathinfo === '/_profiler/import') {
                        return array (  '_controller' => 'web_profiler.controller.profiler:importAction',  '_route' => '_profiler_import',);
                    }

                }

                // _profiler_export
                if (0 === strpos($pathinfo, '/_profiler/export') && preg_match('#^/_profiler/export/(?P<token>[^/\\.]++)\\.txt$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_export')), array (  '_controller' => 'web_profiler.controller.profiler:exportAction',));
                }

                // _profiler_phpinfo
                if ($pathinfo === '/_profiler/phpinfo') {
                    return array (  '_controller' => 'web_profiler.controller.profiler:phpinfoAction',  '_route' => '_profiler_phpinfo',);
                }

                // _profiler_search_results
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/search/results$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_search_results')), array (  '_controller' => 'web_profiler.controller.profiler:searchResultsAction',));
                }

                // _profiler
                if (preg_match('#^/_profiler/(?P<token>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler')), array (  '_controller' => 'web_profiler.controller.profiler:panelAction',));
                }

                // _profiler_router
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/router$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_router')), array (  '_controller' => 'web_profiler.controller.router:panelAction',));
                }

                // _profiler_exception
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/exception$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_exception')), array (  '_controller' => 'web_profiler.controller.exception:showAction',));
                }

                // _profiler_exception_css
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/exception\\.css$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_exception_css')), array (  '_controller' => 'web_profiler.controller.exception:cssAction',));
                }

            }

            if (0 === strpos($pathinfo, '/_configurator')) {
                // _configurator_home
                if (rtrim($pathinfo, '/') === '/_configurator') {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($pathinfo.'/', '_configurator_home');
                    }

                    return array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::checkAction',  '_route' => '_configurator_home',);
                }

                // _configurator_step
                if (0 === strpos($pathinfo, '/_configurator/step') && preg_match('#^/_configurator/step/(?P<index>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_configurator_step')), array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::stepAction',));
                }

                // _configurator_final
                if ($pathinfo === '/_configurator/final') {
                    return array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::finalAction',  '_route' => '_configurator_final',);
                }

            }

        }

        // default_data
        if ($pathinfo === '/default') {
            return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\DefaultController::createDefaultDataAction',  '_route' => 'default_data',);
        }

        if (0 === strpos($pathinfo, '/admin')) {
            // admin_login_check
            if ($pathinfo === '/admin/login-check') {
                return array('_route' => 'admin_login_check');
            }

            // admin_logout
            if ($pathinfo === '/admin/admin/logout') {
                return array('_route' => 'admin_logout');
            }

            // admin_index_page
            if (rtrim($pathinfo, '/') === '/admin') {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', 'admin_index_page');
                }

                return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\RoutingController::loginPageAction',  '_route' => 'admin_index_page',);
            }

            if (0 === strpos($pathinfo, '/admin/admin')) {
                // admin_home_page
                if ($pathinfo === '/admin/admin/home') {
                    return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\RoutingController::homePageAction',  '_route' => 'admin_home_page',);
                }

                // admin_check_failure
                if ($pathinfo === '/admin/admin/logincheck/failure') {
                    return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\RoutingController::loginFailureAction',  '_route' => 'admin_check_failure',);
                }

                if (0 === strpos($pathinfo, '/admin/admin/answer')) {
                    // admin_answer
                    if (rtrim($pathinfo, '/') === '/admin/admin/answer') {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($pathinfo.'/', 'admin_answer');
                        }

                        return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\AnswerController::checkRouteAction',  '_route' => 'admin_answer',);
                    }

                    // admin_answer_delete
                    if (preg_match('#^/admin/admin/answer/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if ($this->context->getMethod() != 'DELETE') {
                            $allow[] = 'DELETE';
                            goto not_admin_answer_delete;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_answer_delete')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\AnswerController::deleteAction',));
                    }
                    not_admin_answer_delete:

                    // admin_answer_update
                    if (preg_match('#^/admin/admin/answer/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if ($this->context->getMethod() != 'PUT') {
                            $allow[] = 'PUT';
                            goto not_admin_answer_update;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_answer_update')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\AnswerController::updateAction',));
                    }
                    not_admin_answer_update:

                    // admin_answer_getbyid
                    if (preg_match('#^/admin/admin/answer/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_admin_answer_getbyid;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_answer_getbyid')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\AnswerController::listAction',));
                    }
                    not_admin_answer_getbyid:

                }

                if (0 === strpos($pathinfo, '/admin/admin/category')) {
                    // admin_category
                    if (rtrim($pathinfo, '/') === '/admin/admin/category') {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($pathinfo.'/', 'admin_category');
                        }

                        return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\CategoryController::checkRouteAction',  '_route' => 'admin_category',);
                    }

                    // admin_category_delete
                    if (preg_match('#^/admin/admin/category/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if ($this->context->getMethod() != 'DELETE') {
                            $allow[] = 'DELETE';
                            goto not_admin_category_delete;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_category_delete')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\CategoryController::deleteAction',));
                    }
                    not_admin_category_delete:

                    // admin_category_update
                    if (preg_match('#^/admin/admin/category/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if ($this->context->getMethod() != 'PUT') {
                            $allow[] = 'PUT';
                            goto not_admin_category_update;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_category_update')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\CategoryController::updateAction',));
                    }
                    not_admin_category_update:

                    // admin_category_getbyid
                    if (preg_match('#^/admin/admin/category/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_admin_category_getbyid;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_category_getbyid')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\CategoryController::listAction',));
                    }
                    not_admin_category_getbyid:

                }

                if (0 === strpos($pathinfo, '/admin/admin/group')) {
                    // admin_group
                    if (rtrim($pathinfo, '/') === '/admin/admin/group') {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($pathinfo.'/', 'admin_group');
                        }

                        return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\GroupController::checkRouteAction',  '_route' => 'admin_group',);
                    }

                    // admin_group_delete
                    if (preg_match('#^/admin/admin/group/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if ($this->context->getMethod() != 'DELETE') {
                            $allow[] = 'DELETE';
                            goto not_admin_group_delete;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_group_delete')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\GroupController::deleteAction',));
                    }
                    not_admin_group_delete:

                    // admin_group_update
                    if (preg_match('#^/admin/admin/group/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if ($this->context->getMethod() != 'PUT') {
                            $allow[] = 'PUT';
                            goto not_admin_group_update;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_group_update')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\GroupController::updateAction',));
                    }
                    not_admin_group_update:

                    // admin_group_getbyid
                    if (preg_match('#^/admin/admin/group/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_admin_group_getbyid;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_group_getbyid')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\GroupController::listAction',));
                    }
                    not_admin_group_getbyid:

                }

                if (0 === strpos($pathinfo, '/admin/admin/t')) {
                    if (0 === strpos($pathinfo, '/admin/admin/team')) {
                        // admin_team
                        if (rtrim($pathinfo, '/') === '/admin/admin/team') {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($pathinfo.'/', 'admin_team');
                            }

                            return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TeamController::checkRouteAction',  '_route' => 'admin_team',);
                        }

                        // admin_team_delete
                        if (preg_match('#^/admin/admin/team/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                            if ($this->context->getMethod() != 'DELETE') {
                                $allow[] = 'DELETE';
                                goto not_admin_team_delete;
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_team_delete')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TeamController::deleteAction',));
                        }
                        not_admin_team_delete:

                        // admin_team_update
                        if (preg_match('#^/admin/admin/team/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                            if ($this->context->getMethod() != 'PUT') {
                                $allow[] = 'PUT';
                                goto not_admin_team_update;
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_team_update')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TeamController::updateAction',));
                        }
                        not_admin_team_update:

                        // admin_team_getbyid
                        if (preg_match('#^/admin/admin/team/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                            if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                                $allow = array_merge($allow, array('GET', 'HEAD'));
                                goto not_admin_team_getbyid;
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_team_getbyid')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TeamController::listAction',));
                        }
                        not_admin_team_getbyid:

                    }

                    if (0 === strpos($pathinfo, '/admin/admin/tournament')) {
                        // admin_tournament
                        if (rtrim($pathinfo, '/') === '/admin/admin/tournament') {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($pathinfo.'/', 'admin_tournament');
                            }

                            return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TournamentController::checkRouteAction',  '_route' => 'admin_tournament',);
                        }

                        // admin_tournament_delete
                        if (preg_match('#^/admin/admin/tournament/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                            if ($this->context->getMethod() != 'DELETE') {
                                $allow[] = 'DELETE';
                                goto not_admin_tournament_delete;
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_tournament_delete')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TournamentController::deleteAction',));
                        }
                        not_admin_tournament_delete:

                        // admin_tournament_update
                        if (preg_match('#^/admin/admin/tournament/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                            if ($this->context->getMethod() != 'PUT') {
                                $allow[] = 'PUT';
                                goto not_admin_tournament_update;
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_tournament_update')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TournamentController::updateAction',));
                        }
                        not_admin_tournament_update:

                        // admin_tournament_getbyid
                        if (preg_match('#^/admin/admin/tournament/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                            if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                                $allow = array_merge($allow, array('GET', 'HEAD'));
                                goto not_admin_tournament_getbyid;
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_tournament_getbyid')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TournamentController::listAction',));
                        }
                        not_admin_tournament_getbyid:

                    }

                }

                if (0 === strpos($pathinfo, '/admin/admin/question')) {
                    // admin_question
                    if (rtrim($pathinfo, '/') === '/admin/admin/question') {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($pathinfo.'/', 'admin_question');
                        }

                        return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\QuestionController::checkRouteAction',  '_route' => 'admin_question',);
                    }

                    // admin_question_delete
                    if (preg_match('#^/admin/admin/question/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if ($this->context->getMethod() != 'DELETE') {
                            $allow[] = 'DELETE';
                            goto not_admin_question_delete;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_question_delete')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\QuestionController::deleteAction',));
                    }
                    not_admin_question_delete:

                    // admin_question_update
                    if (preg_match('#^/admin/admin/question/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if ($this->context->getMethod() != 'PUT') {
                            $allow[] = 'PUT';
                            goto not_admin_question_update;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_question_update')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\QuestionController::updateAction',));
                    }
                    not_admin_question_update:

                    // admin_question_getbyid
                    if (preg_match('#^/admin/admin/question/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_admin_question_getbyid;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_question_getbyid')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\QuestionController::listAction',));
                    }
                    not_admin_question_getbyid:

                }

                if (0 === strpos($pathinfo, '/admin/admin/office')) {
                    // admin_office
                    if (rtrim($pathinfo, '/') === '/admin/admin/office') {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($pathinfo.'/', 'admin_office');
                        }

                        return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\OfficeController::checkRouteAction',  '_route' => 'admin_office',);
                    }

                    // admin_office_delete
                    if (preg_match('#^/admin/admin/office/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if ($this->context->getMethod() != 'DELETE') {
                            $allow[] = 'DELETE';
                            goto not_admin_office_delete;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_office_delete')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\OfficeController::deleteAction',));
                    }
                    not_admin_office_delete:

                    // admin_office_update
                    if (preg_match('#^/admin/admin/office/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if ($this->context->getMethod() != 'PUT') {
                            $allow[] = 'PUT';
                            goto not_admin_office_update;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_office_update')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\OfficeController::updateAction',));
                    }
                    not_admin_office_update:

                    // admin_office_getbyid
                    if (preg_match('#^/admin/admin/office/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_admin_office_getbyid;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_office_getbyid')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\OfficeController::listAction',));
                    }
                    not_admin_office_getbyid:

                }

                if (0 === strpos($pathinfo, '/admin/admin/role')) {
                    // admin_role
                    if (rtrim($pathinfo, '/') === '/admin/admin/role') {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_admin_role;
                        }

                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($pathinfo.'/', 'admin_role');
                        }

                        return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\RoleController::checkRouteAction',  '_route' => 'admin_role',);
                    }
                    not_admin_role:

                    // admin_role_getbyid
                    if (preg_match('#^/admin/admin/role/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_admin_role_getbyid;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'admin_role_getbyid')), array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\RoleController::listAction',));
                    }
                    not_admin_role_getbyid:

                }

            }

            // admin_home
            if ($pathinfo === '/admin/home') {
                return array (  '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\DefaultController::getHomeAction',  '_route' => 'admin_home',);
            }

        }

        // _welcome
        if (rtrim($pathinfo, '/') === '') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', '_welcome');
            }

            return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\WelcomeController::indexAction',  '_route' => '_welcome',);
        }

        if (0 === strpos($pathinfo, '/demo')) {
            if (0 === strpos($pathinfo, '/demo/secured')) {
                if (0 === strpos($pathinfo, '/demo/secured/log')) {
                    if (0 === strpos($pathinfo, '/demo/secured/login')) {
                        // _demo_login
                        if ($pathinfo === '/demo/secured/login') {
                            return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::loginAction',  '_route' => '_demo_login',);
                        }

                        // _demo_security_check
                        if ($pathinfo === '/demo/secured/login_check') {
                            return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::securityCheckAction',  '_route' => '_demo_security_check',);
                        }

                    }

                    // _demo_logout
                    if ($pathinfo === '/demo/secured/logout') {
                        return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::logoutAction',  '_route' => '_demo_logout',);
                    }

                }

                if (0 === strpos($pathinfo, '/demo/secured/hello')) {
                    // acme_demo_secured_hello
                    if ($pathinfo === '/demo/secured/hello') {
                        return array (  'name' => 'World',  '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::helloAction',  '_route' => 'acme_demo_secured_hello',);
                    }

                    // _demo_secured_hello
                    if (preg_match('#^/demo/secured/hello/(?P<name>[^/]++)$#s', $pathinfo, $matches)) {
                        return $this->mergeDefaults(array_replace($matches, array('_route' => '_demo_secured_hello')), array (  '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::helloAction',));
                    }

                    // _demo_secured_hello_admin
                    if (0 === strpos($pathinfo, '/demo/secured/hello/admin') && preg_match('#^/demo/secured/hello/admin/(?P<name>[^/]++)$#s', $pathinfo, $matches)) {
                        return $this->mergeDefaults(array_replace($matches, array('_route' => '_demo_secured_hello_admin')), array (  '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::helloadminAction',));
                    }

                }

            }

            // _demo
            if (rtrim($pathinfo, '/') === '/demo') {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', '_demo');
                }

                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\DemoController::indexAction',  '_route' => '_demo',);
            }

            // _demo_hello
            if (0 === strpos($pathinfo, '/demo/hello') && preg_match('#^/demo/hello/(?P<name>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => '_demo_hello')), array (  '_controller' => 'Acme\\DemoBundle\\Controller\\DemoController::helloAction',));
            }

            // _demo_contact
            if ($pathinfo === '/demo/contact') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\DemoController::contactAction',  '_route' => '_demo_contact',);
            }

        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
