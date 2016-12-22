<?php
$_site = require_once(getenv("SITELOAD")."/siteload.php");
ErrorClass::setNoEmailErrs(true);
ErrorClass::setDevelopment(true);
$S = new $_site->className($_site);

$h->css =<<<EOF
  <style>
body {
  margin: 10px;
  font-family: 'Abel';
  text-align: center;
  width: 580px;
}

.container {
  display: grid;
  grid-template-columns: 180px 180px 180px;
  grid-gap: 10px;
}

.item {
  color: white;
  padding: 1.5em 0;
  font-size: 2em;
}

.a {
  background: #0d9;
}

.b {
  background: #d90;
}

.c {
  background: #9d0;
}

.d {
  background: #90d;
}

.e {
  background: #d09;
}

.f {
  background: #09d;
}

.c {
  grid-row: 2 / 4;
  grid-column: 2 / 4;
}
  </style>
EOF;
list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top
<h1>Using <code>grid-row</code> and <code>grid-column</code></h1>
<div class="container">
  <div class="item a">A</div>
  <div class="item b">B</div>
  <div class="item c">C</div>
  <div class="item d">D</div>
  <div class="item e">E</div>
  <div class="item f">F</div>
</div>
$footer
EOF;
