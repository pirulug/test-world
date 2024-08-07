<?php

class TemplateEngine {
  protected $variables = [];
  protected $blocks = [];
  protected $currentBlock = null;

  public function set($key, $value) {
    $this->variables[$key] = $value;
  }

  public function startBlock($name) {
    $this->currentBlock = $name;
    ob_start();
  }

  public function endBlock() {
    $content = ob_get_clean();
    if ($this->currentBlock) {
      $this->blocks[$this->currentBlock] = $content;
      $this->currentBlock                = null;
    }
  }

  public function render($template) {
    ob_start();
    extract($this->variables);
    include $template;
    return ob_get_clean();
  }

  public function include($template) {
    include $template;
  }

  public function getBlock($name) {
    return $this->blocks[$name] ?? '';
  }
}
