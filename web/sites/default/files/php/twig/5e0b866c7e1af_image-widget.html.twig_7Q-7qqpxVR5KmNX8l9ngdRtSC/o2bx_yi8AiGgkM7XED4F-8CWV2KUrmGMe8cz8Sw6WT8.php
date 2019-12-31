<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* core/themes/claro/templates/content-edit/image-widget.html.twig */
class __TwigTemplate_4283030458efe44ffb893144ee634942fd85aa0cd539454dc337dcd41bdc9930 extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $tags = ["set" => 21, "if" => 35];
        $filters = ["escape" => 30, "without" => 32];
        $functions = [];

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['escape', 'without'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->getSourceContext());

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 21
        $context["classes"] = [0 => "form-managed-file--image", 1 => ((        // line 23
($context["multiple"] ?? null)) ? ("is-multiple") : ("is-single")), 2 => ((        // line 24
($context["upload"] ?? null)) ? ("has-upload") : ("no-upload")), 3 => ((        // line 25
($context["has_value"] ?? null)) ? ("has-value") : ("no-value")), 4 => ((        // line 26
($context["has_meta"] ?? null)) ? ("has-meta") : ("no-meta")), 5 => (($this->getAttribute(        // line 27
($context["data"] ?? null), "preview", [])) ? ("has-preview") : ("no-preview"))];
        // line 30
        echo "<div";
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute($this->getAttribute(($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method"), "removeClass", [0 => "clearfix"], "method")), "html", null, true);
        echo ">
  <div class=\"form-managed-file__main\">
    ";
        // line 32
        echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->env->getExtension('Drupal\Core\Template\TwigExtension')->withoutFilter($this->sandbox->ensureToStringAllowed(($context["data"] ?? null)), "preview", "alt", "title"), "html", null, true);
        echo "
  </div>

  ";
        // line 35
        if ((($context["has_meta"] ?? null) || $this->getAttribute(($context["data"] ?? null), "preview", []))) {
            // line 36
            echo "  <div class=\"form-managed-file__meta-wrapper\">
    <div class=\"form-managed-file__meta\">
      ";
            // line 38
            if ($this->getAttribute(($context["data"] ?? null), "preview", [])) {
                // line 39
                echo "        <div class=\"form-managed-file__image-preview image-preview\">
          <div class=\"image-preview__img-wrapper\">
            ";
                // line 41
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["data"] ?? null), "preview", [])), "html", null, true);
                echo "
          </div>
        </div>
      ";
            }
            // line 45
            echo "
      ";
            // line 46
            if (($this->getAttribute(($context["data"] ?? null), "alt", []) || $this->getAttribute(($context["data"] ?? null), "title", []))) {
                // line 47
                echo "        <div class=\"form-managed-file__meta-items\">
      ";
            }
            // line 49
            echo "
      ";
            // line 50
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["data"] ?? null), "alt", [])), "html", null, true);
            echo "
      ";
            // line 51
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["data"] ?? null), "title", [])), "html", null, true);
            echo "

      ";
            // line 53
            if (($this->getAttribute(($context["data"] ?? null), "alt", []) || $this->getAttribute(($context["data"] ?? null), "title", []))) {
                // line 54
                echo "        </div>
      ";
            }
            // line 56
            echo "    </div>
  </div>
  ";
        }
        // line 59
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "core/themes/claro/templates/content-edit/image-widget.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  125 => 59,  120 => 56,  116 => 54,  114 => 53,  109 => 51,  105 => 50,  102 => 49,  98 => 47,  96 => 46,  93 => 45,  86 => 41,  82 => 39,  80 => 38,  76 => 36,  74 => 35,  68 => 32,  62 => 30,  60 => 27,  59 => 26,  58 => 25,  57 => 24,  56 => 23,  55 => 21,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "core/themes/claro/templates/content-edit/image-widget.html.twig", "/var/www/html/web/core/themes/claro/templates/content-edit/image-widget.html.twig");
    }
}
