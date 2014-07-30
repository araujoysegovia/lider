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
\t\t
\t\t<link rel=\"stylesheet\" href=\"";
        // line 11
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/css/bootstrap.css"), "html", null, true);
        echo "\" />\t\t
\t\t<link rel=\"stylesheet\" href=\"";
        // line 12
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/css/login.css"), "html", null, true);
        echo "\" />\t\t
\t</head>
\t<body>\t
\t\t<div class=\"body\">\t
\t\t\t<div class=\"login\">
\t\t\t\t<div class=\"logo\">
\t\t\t\t\t<img src=\"";
        // line 18
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/lider/images/logoAyS.png"), "html", null, true);
        echo "\" alt=\"Araujo & Segovia\"/>
\t\t\t\t</div>
\t\t\t\t<div class= \"login-form\">\t
\t\t\t\t\t<form role=\"form\" class=\"form-class\" action=\"";
        // line 21
        echo $this->env->getExtension('routing')->getPath("admin_login_check");
        echo "\" method=\"POST\">
\t\t\t\t\t\t  <div class=\"form-group\">\t\t\t\t    
\t\t\t\t\t\t    <input type=\"email\" class=\"form-control\" id=\"email\" placeholder=\"Correo electr&oacute;nico\">
\t\t\t\t\t\t  </div>
\t\t\t\t\t\t  <div class=\"form-group\">
\t\t\t\t\t\t    <input type=\"password\" class=\"form-control\" id=\"password\" placeholder=\"Contrase&ntilde;a\">
\t\t\t\t\t\t  </div>\t\t\t\t 
\t\t\t\t\t\t  <button type=\"submit\" class=\"btn btn-primary btn-ingresar\">Ingresar</button>
\t\t\t\t\t\t  
\t\t\t\t\t\t  ";
        // line 30
        if ((isset($context["error"]) ? $context["error"] : $this->getContext($context, "error"))) {
            // line 31
            echo "\t\t\t\t\t\t\t<div class=\"alert alert-danger\">
\t\t\t\t\t\t\t \t";
            // line 32
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["error"]) ? $context["error"] : $this->getContext($context, "error")), "message"), "html", null, true);
            echo "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t  ";
        }
        // line 34
        echo "\t
\t\t\t\t\t\t <span id=\"signinButton\">
\t\t\t\t\t\t \t<span
    \t\t\t\t\t\t\tclass=\"g-signin\"
\t\t\t\t\t\t\t\tdata-callback=\"signinCallback\"
    \t\t\t\t\t\t\tdata-clientid=\"409288717107-h73ade2t3homia8e8r6o4dg3bmuingn3.apps.googleusercontent.com\"
    \t\t\t\t\t\t\tdata-cookiepolicy=\"single_host_origin\"
    \t\t\t\t\t\t\tdata-requestvisibleactions=\"http://schemas.google.com/AddActivity\"
    \t\t\t\t\t\t\tdata-scope=\"https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email\">
  \t\t\t\t\t\t\t</span>
\t\t\t\t\t\t</span>\t\t  
\t\t\t\t\t</form>\t\t\t\t\t\t\t
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t<script type=\"text/javascript\">

    </script>\t\t
\t</body>\t
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
        return array (  91 => 34,  85 => 32,  82 => 31,  80 => 30,  68 => 21,  62 => 18,  53 => 12,  49 => 11,  44 => 9,  40 => 8,  36 => 7,  32 => 6,  28 => 5,  24 => 4,  19 => 1,);
    }
}
