<?php

/* LiderBundle:Lider:home.html.twig */
class __TwigTemplate_e4ecdf8cdbacdc9a1a7b67b85b69ff114177ff230fc13ff78d114147850d37fc extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
\t<head>
\t\t<meta charset=\"UTF-8\">
\t\t<script src= \"";
        // line 5
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/jquery.min.js"), "html", null, true);
        echo "\"></script>\t
\t\t<script src= \"";
        // line 6
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/backbone.js"), "html", null, true);
        echo "\"></script>\t
\t\t<script src= \"";
        // line 7
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/underscore.js"), "html", null, true);
        echo "\"></script>\t
\t\t<script src= \"";
        // line 8
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/bootstrap.min.js"), "html", null, true);
        echo "\"></script>\t\t    
    <script src= \"";
        // line 9
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/kendo/js/kendo.all.min.js"), "html", null, true);
        echo "\"></script>   
    <script src= \"";
        // line 10
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/kendo/js/cultures/kendo.culture.es-CO.min.js"), "html", null, true);
        echo "\"></script>   
\t\t<link rel=\"stylesheet\" href=\"";
        // line 11
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/css/bootstrap.css"), "html", null, true);
        echo "\" />\t\t
\t\t<link rel=\"stylesheet\" href=\"";
        // line 12
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/css/homeAdministrator.css"), "html", null, true);
        echo "\" />

    <link href=\"";
        // line 14
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/kendo/styles/kendo.common.min.css"), "html", null, true);
        echo "\"rel=\"stylesheet\" />
    <link href=\"";
        // line 15
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/kendo/styles/kendo.bootstrap.min.css"), "html", null, true);
        echo "\"rel=\"stylesheet\" />
    
    <script type=\"text/javascript\" src= \"";
        // line 17
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/homeAdministrator.js"), "html", null, true);
        echo "\"></script>
\t</head>
\t<body>
    <div class=\"body\">
      <nav class=\"navbar navbar-default\" role=\"navigation\">
        <div class=\"container-fluid\">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class=\"navbar-header\">
            <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#bs-example-navbar-collapse-1\">
              <span class=\"sr-only\">Toggle navigation</span>
              <span class=\"icon-bar\"></span>
              <span class=\"icon-bar\"></span>
              <span class=\"icon-bar\"></span>
            </button>
            <img src=\"";
        // line 31
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/images/logoAyS.png"), "html", null, true);
        echo "\" alt=\"Araujo & Segovia\" width=\"220px\"/>
          </div>

          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class=\"collapse navbar-collapse\" id=\"bs-example-navbar-collapse-1\">
            <ul class=\"nav navbar-nav\">                
              <li class=\"dropdown\">
                <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Administrador<span class=\"caret\"></span></a>
                <ul class=\"dropdown-menu\" role=\"menu\">
                  <li><a id=\"linkTournaments\" href=\"#\">Torneos</a></li>
                  <li><a id=\"linkPlayers\" href=\"#\">Jugadores</a></li>
                  <li><a id =\"linkGroups\" href=\"#\">Grupos</a></li>            
                </ul>
              </li>
              <li class=\"dropdown\">
                <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Preguntas<span class=\"caret\"></span></a>
                <ul class=\"dropdown-menu\" role=\"menu\">
                  <li><a id =\"linkCategories\" href=\"#\">Categorias</a></li>
                  <li><a id=\"linkQuestions\" href=\"#\">Preguntas</a></li>            
                </ul>
              </li>        
            </ul>                     
          </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
      </nav>

      <div  class =\"tournaments\" >

          <h3 class=\"title\">Torneos</h3>
          <div id=\"gridTournaments\"></div>
                  
      </div>

      <div  class =\"players\" >

          <h3 class=\"title\">Jugadores</h3>
          <div id=\"gridPlayers\"></div>
                  
      </div>

      <div  class =\"groups\" >

          <h3 class=\"title\">Grupos</h3>
          <div id=\"gridGroups\"></div>
                  
      </div>            

      <div  class =\"categories\" >

          <h3 class=\"title\">Categorías</h3>
          <div id=\"gridCategories\"></div>
                  
      </div> 

      <div  class =\"questions\" >

          <h3 class=\"title\">Preguntas</h3>
          <div id=\"gridQuestions\"></div>
                  
      </div>       

    </div>
\t</body>
</html>";
    }

    public function getTemplateName()
    {
        return "LiderBundle:Lider:home.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  84 => 31,  58 => 14,  53 => 12,  480 => 162,  474 => 161,  469 => 158,  461 => 155,  457 => 153,  453 => 151,  444 => 149,  440 => 148,  437 => 147,  435 => 146,  430 => 144,  427 => 143,  423 => 142,  413 => 134,  409 => 132,  407 => 131,  402 => 130,  398 => 129,  393 => 126,  387 => 122,  384 => 121,  381 => 120,  379 => 119,  374 => 116,  368 => 112,  365 => 111,  362 => 110,  360 => 109,  355 => 106,  341 => 105,  337 => 103,  322 => 101,  314 => 99,  312 => 98,  309 => 97,  305 => 95,  298 => 91,  294 => 90,  285 => 89,  283 => 88,  278 => 86,  268 => 85,  264 => 84,  258 => 81,  252 => 80,  247 => 78,  241 => 77,  229 => 73,  220 => 70,  214 => 69,  177 => 65,  169 => 60,  140 => 55,  132 => 51,  128 => 49,  107 => 36,  61 => 13,  273 => 96,  269 => 94,  254 => 92,  243 => 88,  240 => 86,  238 => 85,  235 => 74,  230 => 82,  227 => 81,  224 => 71,  221 => 77,  219 => 76,  217 => 75,  208 => 68,  204 => 72,  179 => 69,  159 => 61,  143 => 56,  135 => 53,  119 => 42,  102 => 32,  71 => 17,  67 => 17,  63 => 15,  59 => 14,  28 => 5,  201 => 92,  196 => 90,  183 => 82,  171 => 61,  166 => 71,  163 => 62,  158 => 67,  156 => 66,  151 => 63,  142 => 59,  138 => 54,  136 => 56,  121 => 46,  117 => 44,  105 => 40,  91 => 34,  62 => 15,  49 => 11,  94 => 28,  89 => 20,  85 => 32,  75 => 17,  68 => 21,  56 => 9,  87 => 25,  21 => 2,  31 => 4,  38 => 6,  26 => 6,  24 => 4,  25 => 5,  19 => 1,  93 => 28,  88 => 6,  78 => 21,  46 => 7,  44 => 9,  27 => 4,  79 => 18,  72 => 16,  69 => 25,  47 => 9,  40 => 8,  37 => 8,  22 => 2,  246 => 90,  157 => 56,  145 => 46,  139 => 45,  131 => 52,  123 => 47,  120 => 40,  115 => 43,  111 => 37,  108 => 36,  101 => 32,  98 => 31,  96 => 31,  83 => 25,  74 => 14,  66 => 24,  55 => 15,  52 => 21,  50 => 10,  43 => 6,  41 => 9,  35 => 5,  32 => 6,  29 => 6,  209 => 82,  203 => 78,  199 => 67,  193 => 73,  189 => 71,  187 => 84,  182 => 66,  176 => 64,  173 => 65,  168 => 72,  164 => 59,  162 => 57,  154 => 58,  149 => 51,  147 => 58,  144 => 49,  141 => 48,  133 => 55,  130 => 41,  125 => 44,  122 => 43,  116 => 41,  112 => 42,  109 => 34,  106 => 36,  103 => 32,  99 => 31,  95 => 28,  92 => 21,  86 => 28,  82 => 31,  80 => 30,  73 => 19,  64 => 14,  60 => 6,  57 => 11,  54 => 10,  51 => 14,  48 => 8,  45 => 10,  42 => 7,  39 => 9,  36 => 7,  33 => 7,  30 => 7,);
    }
}
