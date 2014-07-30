<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * appDevUrlGenerator
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appDevUrlGenerator extends Symfony\Component\Routing\Generator\UrlGenerator
{
    private static $declaredRoutes = array(
        '_wdt' => array (  0 =>   array (    0 => 'token',  ),  1 =>   array (    '_controller' => 'web_profiler.controller.profiler:toolbarAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'token',    ),    1 =>     array (      0 => 'text',      1 => '/_wdt',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler_home' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'web_profiler.controller.profiler:homeAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/_profiler/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler_search' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'web_profiler.controller.profiler:searchAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/_profiler/search',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler_search_bar' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'web_profiler.controller.profiler:searchBarAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/_profiler/search_bar',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler_purge' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'web_profiler.controller.profiler:purgeAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/_profiler/purge',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler_info' => array (  0 =>   array (    0 => 'about',  ),  1 =>   array (    '_controller' => 'web_profiler.controller.profiler:infoAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'about',    ),    1 =>     array (      0 => 'text',      1 => '/_profiler/info',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler_import' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'web_profiler.controller.profiler:importAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/_profiler/import',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler_export' => array (  0 =>   array (    0 => 'token',  ),  1 =>   array (    '_controller' => 'web_profiler.controller.profiler:exportAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '.txt',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/\\.]++',      3 => 'token',    ),    2 =>     array (      0 => 'text',      1 => '/_profiler/export',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler_phpinfo' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'web_profiler.controller.profiler:phpinfoAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/_profiler/phpinfo',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler_search_results' => array (  0 =>   array (    0 => 'token',  ),  1 =>   array (    '_controller' => 'web_profiler.controller.profiler:searchResultsAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/search/results',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'token',    ),    2 =>     array (      0 => 'text',      1 => '/_profiler',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler' => array (  0 =>   array (    0 => 'token',  ),  1 =>   array (    '_controller' => 'web_profiler.controller.profiler:panelAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'token',    ),    1 =>     array (      0 => 'text',      1 => '/_profiler',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler_router' => array (  0 =>   array (    0 => 'token',  ),  1 =>   array (    '_controller' => 'web_profiler.controller.router:panelAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/router',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'token',    ),    2 =>     array (      0 => 'text',      1 => '/_profiler',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler_exception' => array (  0 =>   array (    0 => 'token',  ),  1 =>   array (    '_controller' => 'web_profiler.controller.exception:showAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/exception',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'token',    ),    2 =>     array (      0 => 'text',      1 => '/_profiler',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_profiler_exception_css' => array (  0 =>   array (    0 => 'token',  ),  1 =>   array (    '_controller' => 'web_profiler.controller.exception:cssAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/exception.css',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'token',    ),    2 =>     array (      0 => 'text',      1 => '/_profiler',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_configurator_home' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::checkAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/_configurator/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_configurator_step' => array (  0 =>   array (    0 => 'index',  ),  1 =>   array (    '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::stepAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'index',    ),    1 =>     array (      0 => 'text',      1 => '/_configurator/step',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_configurator_final' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::finalAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/_configurator/final',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'default_data' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\DefaultController::createDefaultDataAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/default',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_login_check' => array (  0 =>   array (  ),  1 =>   array (  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/login-check',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_logout' => array (  0 =>   array (  ),  1 =>   array (  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/admin/logout',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_index_page' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\RoutingController::loginPageAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_home_page' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\RoutingController::homePageAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/admin/home',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_check_failure' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\RoutingController::loginFailureAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/admin/logincheck/failure',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_answer' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\AnswerController::checkRouteAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/admin/answer/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_answer_delete' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\AnswerController::deleteAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'DELETE',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/answer',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_answer_update' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\AnswerController::updateAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'PUT',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/answer',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_answer_getbyid' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\AnswerController::listAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/answer',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_category' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\CategoryController::checkRouteAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/admin/category/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_category_delete' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\CategoryController::deleteAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'DELETE',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/category',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_category_update' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\CategoryController::updateAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'PUT',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/category',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_category_getbyid' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\CategoryController::listAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/category',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_group' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\GroupController::checkRouteAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/admin/group/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_group_delete' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\GroupController::deleteAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'DELETE',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/group',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_group_update' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\GroupController::updateAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'PUT',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/group',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_group_getbyid' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\GroupController::listAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/group',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_team' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TeamController::checkRouteAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/admin/team/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_team_delete' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TeamController::deleteAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'DELETE',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/team',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_team_update' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TeamController::updateAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'PUT',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/team',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_team_getbyid' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TeamController::listAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/team',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_tournament' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TournamentController::checkRouteAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/admin/tournament/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_tournament_delete' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TournamentController::deleteAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'DELETE',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/tournament',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_tournament_update' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TournamentController::updateAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'PUT',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/tournament',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_tournament_getbyid' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\TournamentController::listAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/tournament',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_question' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\QuestionController::checkRouteAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/admin/question/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_question_delete' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\QuestionController::deleteAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'DELETE',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/question',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_question_update' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\QuestionController::updateAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'PUT',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/question',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_question_getbyid' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\QuestionController::listAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/question',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_office' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\OfficeController::checkRouteAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/admin/office/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_office_delete' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\OfficeController::deleteAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'DELETE',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/office',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_office_update' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\OfficeController::updateAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'PUT',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/office',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_office_getbyid' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\OfficeController::listAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/office',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_role' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\RoleController::checkRouteAction',  ),  2 =>   array (    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/admin/role/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_role_getbyid' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\RoleController::listAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/admin/admin/role',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'admin_home' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Lider\\Bundle\\LiderBundle\\Controller\\DefaultController::getHomeAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/admin/home',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_welcome' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Acme\\DemoBundle\\Controller\\WelcomeController::indexAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_demo_login' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::loginAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/demo/secured/login',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_demo_security_check' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::securityCheckAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/demo/secured/login_check',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_demo_logout' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::logoutAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/demo/secured/logout',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'acme_demo_secured_hello' => array (  0 =>   array (  ),  1 =>   array (    'name' => 'World',    '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::helloAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/demo/secured/hello',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_demo_secured_hello' => array (  0 =>   array (    0 => 'name',  ),  1 =>   array (    '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::helloAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'name',    ),    1 =>     array (      0 => 'text',      1 => '/demo/secured/hello',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_demo_secured_hello_admin' => array (  0 =>   array (    0 => 'name',  ),  1 =>   array (    '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::helloadminAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'name',    ),    1 =>     array (      0 => 'text',      1 => '/demo/secured/hello/admin',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_demo' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Acme\\DemoBundle\\Controller\\DemoController::indexAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/demo/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_demo_hello' => array (  0 =>   array (    0 => 'name',  ),  1 =>   array (    '_controller' => 'Acme\\DemoBundle\\Controller\\DemoController::helloAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'name',    ),    1 =>     array (      0 => 'text',      1 => '/demo/hello',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        '_demo_contact' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Acme\\DemoBundle\\Controller\\DemoController::contactAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/demo/contact',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
    );

    /**
     * Constructor.
     */
    public function __construct(RequestContext $context, LoggerInterface $logger = null)
    {
        $this->context = $context;
        $this->logger = $logger;
    }

    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        if (!isset(self::$declaredRoutes[$name])) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
        }

        list($variables, $defaults, $requirements, $tokens, $hostTokens, $requiredSchemes) = self::$declaredRoutes[$name];

        return $this->doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);
    }
}
