import $ from 'jquery';

// avec $ on recupere le jQuery  que on stoke plus haut dans la var $ 
// et la ou il y'a le global.$  et global.jQuery  on le stoke dans $
global.$ = global.jQuery = $;
// dans global.jQuery j'ai le $
// dans global.$ j'ai aussi le  $ 
// donc on stock jQuery dans la variable globale 
// pour le appeler par tout ou il ya le pluging jQuery

import 'bootstrap';