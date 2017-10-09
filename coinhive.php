<?php
// Use coinhive to mine bit coins.  
$_site = require_once(getenv("SITELOADNAME"));
$S = new $_site->className($_site);

// https://coinhive.com. Public and private keys:
// public: kSnTZA1eUWH14vaYLoBxuK07csD66Uhg
// private: Cv0rWRwGD3yypzn1OPo8pf6sxzoukwAM
//
// My Monero key (https://mymonero.com/) Login Key (private):
// does niche ruined pancakes ruling hacksaw raking icing biology jackets silk eden hacksaw
// My Monero address:
// 43tR5jNAX138bzZoBLPj3NCDVURwLMpGHM7yPCmvor2J1MZbu62zcPCHZC27rNq68hJykkhdnNzkab4fjxz6QTAAMgr4vaT
// View key (private):
// e8f7ca28de939122880ecc9c6b65dd7c22b05874de561eb9b83fc945f4712f02
// Send key (private):
// 3cb93659ec0cfbd42133a74b75a4d53cb883372649a228823c6a572ae8efeb02
// Password: BLP6706424

$h->extra =<<<EOF
  <script src="https://coinhive.com/lib/miner.min.js" async></script>
EOF;
$h->css =<<<EOF
  <style>
.coinhive-miner {
  width: 265px;
  height: 310px;
  margin: auto;
}
  </style>
EOF;

list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top  
<div class="coinhive-miner" 
	data-key="kSnTZA1eUWH14vaYLoBxuK07csD66Uhg">
	<em>Please disable Adblock!</em>
</div>
$footer
EOF;
