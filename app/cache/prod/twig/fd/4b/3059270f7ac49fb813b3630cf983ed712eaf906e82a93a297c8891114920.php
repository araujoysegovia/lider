<?php

/* LiderBundle:Lider:index.html.twig */
class __TwigTemplate_fd4b3059270f7ac49fb813b3630cf983ed712eaf906e82a93a297c8891114920 extends Twig_Template
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
        echo "<html>
\t<head>\t\t
\t\t<meta charset=\"UTF-8\">
\t\t<script src= \"";
        // line 4
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/jquery.min.js"), "html", null, true);
        echo "\"></script>\t
\t\t<script src= \"";
        // line 5
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/underscore.js"), "html", null, true);
        echo "\"></script>
\t\t<script src= \"";
        // line 6
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/backbone.js"), "html", null, true);
        echo "\"></script>\t\t
\t\t<script src= \"";
        // line 7
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/bootstrap.min.js"), "html", null, true);
        echo "\"></script>\t\t
\t\t<script src= \"";
        // line 8
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/login.js"), "html", null, true);
        echo "\"></script>\t
\t\t<script src= \"";
        // line 9
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/js/notify.min.js"), "html", null, true);
        echo "\"></script>\t
\t\t<link rel=\"stylesheet\" href=\"";
        // line 10
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/css/bootstrap.css"), "html", null, true);
        echo "\" />\t\t
\t\t<link rel=\"stylesheet\" href=\"";
        // line 11
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/css/login.css"), "html", null, true);
        echo "\" />\t\t
\t</head>
\t<body>\t
\t\t<div class=\"body\">\t
\t\t\t<div class=\"login\">
\t\t\t\t<div class=\"logo\">
\t\t\t\t\t<img src=\"";
        // line 17
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/images/logoAyS.png"), "html", null, true);
        echo "\" alt=\"Araujo & Segovia\"/>
\t\t\t\t</div>
\t\t\t\t<div class= \"login-form\">
\t\t\t\t\t<form role=\"form\"  enctype=\"multipart/form-data\" >
\t\t\t\t\t  <div class=\"form-group\">\t\t\t\t    
\t\t\t\t\t    <input type=\"email\" class=\"form-control\" id=\"email\" placeholder=\"Correo electr&oacute;nico\">
\t\t\t\t\t  </div>
\t\t\t\t\t  <div class=\"form-group\">

\t\t\t\t\t    <input type=\"password\" class=\"form-control\" id=\"password\" placeholder=\"Contrase&ntilde;a\">
\t\t\t\t\t  </div>\t\t\t\t 
\t\t\t\t\t  <button type=\"submit\" class=\"btn btn-primary\">Ingresar</button>
\t\t\t\t\t</form>\t\t\t\t\t
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</body>
</html>";
    }

    public function getTemplateName()
    {
        return "LiderBundle:Lider:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  61 => 17,  52 => 11,  48 => 10,  44 => 9,  40 => 8,  36 => 7,  32 => 6,  28 => 5,  24 => 4,  19 => 1,);
    }
}
