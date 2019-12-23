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

/* @claro/entity-add-list.html.twig */
class __TwigTemplate_a69fb6a8e247d573bb5925ccd7251ddf45b4613cbb87067877eec871ac6fd7bd extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $tags = ["set" => 19, "if" => 23, "for" => 25];
        $filters = ["escape" => 24, "without" => 34];
        $functions = ["create_attribute" => 31];

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'for'],
                ['escape', 'without'],
                ['create_attribute']
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
        // line 19
        $context["item_classes"] = [0 => "admin-item"];
        // line 23
        if ( !twig_test_empty(($context["bundles"] ?? null))) {
            // line 24
            echo "  <dl";
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["attributes"] ?? null), "addClass", [0 => "admin-list"], "method")), "html", null, true);
            echo ">
    ";
            // line 25
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["bundles"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["bundle"]) {
                // line 26
                echo "      ";
                // line 30
                echo "      ";
                $context["bundle_attributes"] = (($this->getAttribute($this->getAttribute($this->getAttribute($context["bundle"], "add_link", []), "url", []), "getOption", [0 => "attributes"], "method")) ? ($this->getAttribute($this->getAttribute($this->getAttribute($context["bundle"], "add_link", []), "url", []), "getOption", [0 => "attributes"], "method")) : ([]));
                // line 31
                echo "      ";
                $context["link_attributes"] = $this->getAttribute($this->env->getExtension('Drupal\Core\Template\TwigExtension')->createAttribute($this->sandbox->ensureToStringAllowed(($context["bundle_attributes"] ?? null))), "addClass", [0 => "admin-item__link"], "method");
                // line 32
                echo "      <div";
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->env->getExtension('Drupal\Core\Template\TwigExtension')->createAttribute(["class" => ($context["item_classes"] ?? null)]), "html", null, true);
                echo ">
        <dt class=\"admin-item__title\">
          <a href=\"";
                // line 34
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute($this->getAttribute($context["bundle"], "add_link", []), "url", [])), "html", null, true);
                echo "\"";
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->env->getExtension('Drupal\Core\Template\TwigExtension')->withoutFilter($this->sandbox->ensureToStringAllowed(($context["link_attributes"] ?? null)), "href"), "html", null, true);
                echo ">
            ";
                // line 35
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute($this->getAttribute($context["bundle"], "add_link", []), "text", [])), "html", null, true);
                echo "
          </a>
        </dt>
        ";
                // line 39
                echo "        ";
                if ($this->getAttribute($context["bundle"], "description", [])) {
                    // line 40
                    echo "          <dd class=\"admin-item__description\">";
                    echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute($context["bundle"], "description", [])), "html", null, true);
                    echo "</dd>
        ";
                }
                // line 42
                echo "      </div>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['bundle'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 44
            echo "  </dl>
";
        } elseif ( !twig_test_empty(        // line 45
($context["add_bundle_message"] ?? null))) {
            // line 46
            echo "  <p>
    ";
            // line 47
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["add_bundle_message"] ?? null)), "html", null, true);
            echo "
  </p>
";
        }
    }

    public function getTemplateName()
    {
        return "@claro/entity-add-list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  118 => 47,  115 => 46,  113 => 45,  110 => 44,  103 => 42,  97 => 40,  94 => 39,  88 => 35,  82 => 34,  76 => 32,  73 => 31,  70 => 30,  68 => 26,  64 => 25,  59 => 24,  57 => 23,  55 => 19,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "@claro/entity-add-list.html.twig", "/var/www/html/web/core/themes/claro/templates/entity-add-list.html.twig");
    }
}
