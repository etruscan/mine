<?php 
define('wR', 'http://gog.orttec.com/gameStores/'); 
//define('wR', 'http://localhost:81/gameStores/'); 
class germangames { 
  var $name = 'German Games'; 
  function curl($url) { 
    $curl = curl_init($url); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
    $xml = curl_exec($curl); 
    curl_close($curl); 
    return $xml; 
  } 

  function search($find) { 
    $games = array(); 
    $page = $this->curl('http://www.germangames.com/catalogsearch/result/index/?limit=50&q='.urlencode($find)); 
    preg_match('#"amount">(.*?)</p>#ms', $page, $match); 
    $games['info'] = trim(strip_tags($match[1])); 
    preg_match_all('#"product-name"><a.*?>(.*?)<.*?"price-box">(.*?)</div>.*?(Notify me when|Add to Cart)#ms', $page, $matches, PREG_SET_ORDER); 
    foreach($matches as $match) { 
      $tGame = array(); 
      preg_match_all('#"price".*?>(.*?)<#ms', $match[2], $prices, PREG_SET_ORDER); 
      $tGame['name'] = trim($match[1]); 
      $tGame['price'] = trim($prices[0][1]); 
      $tGame['salePrice'] = isset($prices[1][1]) ? trim($prices[1][1]) : $prices[0][1]; 
      $tGame['stock'] = ($match[3] == 'Add to Cart') ? 'Yes' : 'No'; 
      $games[$tGame['name']] = $tGame; 
    } 
    return $games; 
  } 
} 
class boardgamebliss { 
  var $name = 'Board Game Bliss'; 
  function curl($url) { 
    $curl = curl_init($url); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
    $xml = curl_exec($curl); 
    curl_close($curl); 
    return $xml; 
  } 
  function search($find) { 
    $games = array(); 
    $page = $this->curl('http://www.boardgamebliss.com/search?q='.urlencode($find)); 
    preg_match('#Page 1 of (.*?)<#ms', $page, $match); 
    $games['info'] = isset($match[1]) ? 'Page 1 of '.$match[1] : 'Page 1 of 1'; 
    preg_match_all('#"row results".*?title="">(.*?)</a>.*?"search-price">(.*?)</span>#ms', $page, $matches, PREG_SET_ORDER); 
    foreach($matches as $match) { 
      if(trim($match[2]) == "") continue; 
      $tGame = array(); 
      $tGame['name'] = trim(strip_tags($match[1])); 
      $tGame['price'] = trim($match[2]); 
      $tGame['salePrice'] = $tGame['price']; 
      $tGame['stock'] = 'Unknown'; 
      $games[$tGame['name']] = $tGame; 
    } 
    return $games; 
  } 
} 
class geekstopgames { 
  var $name = 'GeekStop Games'; 
  function curl($url) { 
    $curl = curl_init($url); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
    $xml = curl_exec($curl); 
    curl_close($curl); 
    return $xml; 
  } 

  function search($find) { 
    $games = array(); 
    $page = $this->curl('http://geekstopgames.com/gameSearch.php?search='.urlencode($find)); 
    preg_match('#<h3>(.*?) Games Found#ms', $page, $match); 
    $games['info'] = $match[1].' Games Found'; 
    preg_match_all('#"imageCol">.*?<a.*?>(.*?)</a>.*?<span>(.*?)</span><br/>.*?(Out of Stock|In Stock|Coming Soon)#ms', $page, $matches, PREG_SET_ORDER); 
    foreach($matches as $match) { 
      $tGame = array(); 
      $found = preg_match_all('#<span .*?>(.*?)<#ms', trim($match[2]), $prices, PREG_SET_ORDER); 
      $tGame['name'] = trim($match[1]); 
      if(!($found)) { 
        $tGame['price'] = trim($match[2]); 
        $tGame['salePrice'] = trim($match[2]); 
      } 
      else { 
        $tGame['price'] = trim($prices[0][1]); 
        $tGame['salePrice'] = trim($prices[1][1]); 
      } 
      $tGame['stock'] = 'Yes'; 
      if($match[3] == 'Out of Stock') $tGame['stock'] = 'No'; 
      else if($match[3] == 'Coming Soon') $tGame['stock'] = 'Soon'; 
      $games[$tGame['name']] = $tGame; 
    } 
    return $games; 
  } 
} 

class cultofthenew { 
  var $name = 'Cult of the New'; 
  function curl($url) { 
    $curl = curl_init($url); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
    $xml = curl_exec($curl); 
    curl_close($curl); 
    return $xml; 
  } 

  function search($find) { 
    $games = array(); 
    $page = $this->curl('http://www.thecultofthenew.com/catalogsearch/result/index/?limit=all&q='.urlencode($find)); 
    preg_match('#"amount".*?<strong>(.*?)</strong>#ms', $page, $match); 
    if(!(isset($match[1]))) { 
      $games['info'] = 'No games found'; 
      return $games; 
    } 
    $games['info'] = $match[1]; 
    preg_match_all('#"product-name-height">(.*?)<.*?"retail-price-box">(.*?)</div>.*?"price">(.*?)<.*?<button.*?title="(.*?)"#ms', $page, $matches, PREG_SET_ORDER); 
    foreach($matches as $match) { 
      $tGame = array(); 
      $tGame['name'] = trim($match[1]); 
      $price = trim(strip_tags($match[2])); 
      $percent = (trim(str_replace("%", "", $match[3])) / 100); 
      if($match[4] == 'Pre-Order') $tGame['stock'] = 'Pre-Order'; 
      else if($match[4] == 'Notify Me') $tGame['stock'] = 'No'; 
      else { 
        $tGame['stock'] = 'Yes'; 
        $price = $match[4]; 
      } 
      $tGame['salePrice'] = $price; 
      $price = str_replace("$", "", $price); 
      $tGame['price'] = '$'.number_format($price / (1 - $percent),2); 
      $tGame['percent'] = $percent; 
      $games[$tGame['name']] = $tGame; 
    } 
    return $games; 
  } 
} 

class template { 
  var $db; 
  var $header; 
  var $jQueryHeader; 
  var $jQuery; 
  var $jQueryFooter; 
  var $java; 
  var $content; 
  var $tabHeader; 
  var $tabs; 
  var $tabFooter; 
  var $footer; 

  var $stores; 
  function __construct() { 
    $this->stores = array(); 
    $this->stores['geekstopgames']  = new geekstopgames(); 
    $this->stores['germangames']    = new germangames(); 
    $this->stores['boardgamebliss'] = new boardgamebliss(); 
    $this->stores['cultofthenew']   = new cultofthenew(); 
    if(isset($_REQUEST['task'])) { 
      $task = $_REQUEST['task'].'Task'; 
      if(method_exists($this, $task)) $this->$task(); 
    } 
    $this->header = '<!DOCTYPE html>'. 
      '<html>'. 
      '<head>'. 
      ' <title>Ontario Online Game Search</title>'. 
      ' <link type="text/css" href="'.wR.'css/smoothness/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />'. 
      ' <link type="text/css" href="'.wR.'css/tableSorter/style.css" rel="stylesheet" />'."\n". 
      ' <link type="text/css" href="'.wR.'css/style.css" rel="stylesheet" />'. 
      ' <script type="text/javascript" src="'.wR.'js/jquery-1.9.1.min.js"></script>'. 
      ' <script type="text/javascript" src="'.wR.'js/jquery-ui-1.10.3.custom.min.js"></script>'. 
      ' <script type="text/javascript" src="'.wR.'js/jquery.blockUI.js"></script>'. 
      ' <script type="text/javascript" src="'.wR.'js/jquery.tablesorter.min.js"></script>'. 
      ' <script type="text/javascript" src="'.wR.'js/jquery.tablesorter.repeat.js"></script>'. 
      ' <script type="text/javascript" src="'.wR.'js/jquery.tablesorter.select.js"></script>'; 
    $this->jQueryHeader = ''. 
      ' <script type="text/javascript">'. 
      'var blockCount = 0;'. 
      '  $(document).ready(function() {'. 
      'function blockUI() {'. 
      '  window.blockCount++;'. 
      '  if(window.blockCount == 1) {'. 
      '    $.blockUI({'. 
      '      css: { '. 
      '        border: "none", '. 
      '        padding: "15px", '. 
      '        backgroundColor: "#000000", '. 
      '        "-webkit-border-radius": "10px",'.  
      '        "-moz-border-radius": "10px", '. 
      '        opacity: .5, '. 
      '        color: "#FFFFFF" '. 
      '      }'. 
      '    }); '. 
      '  }'. 
      '}'. 
      'function unblockUI() {'. 
      '  window.blockCount--;'. 
      '  if(window.blockCount < 0) {'. 
      '    window.blockCount = 0;'. 
      '  }'. 
      '  if(window.blockCount == 0) {'. 
      '    $.unblockUI();'. 
      '  }'. 
      '}'; 
     
    $this->jQuery = ''; 
    $this->jQueryFooter = ''. 
      '  });'; 
    $this->java = ''. 
      'function updateTips( box, t ) {'. 
      ' box'. 
      '   .text( t )'. 
      '   .addClass( "ui-state-highlight" );'. 
      ' setTimeout(function() {'. 
      '   box.removeClass( "ui-state-highlight", 1500 );'. 
      ' }, 500 );'. 
      '}'. 
      'function checkLength( o, n, min, max, box ) {'. 
      '  if ( o.val().length > max || o.val().length < min ) {'. 
      '    o.addClass( "ui-state-error" );'. 
      '    updateTips( box, "Length of " + n + " must be between " + min + " and " + max + "." );'. 
      '    return false;'. 
      '  } else {'. 
      '    return true;'. 
      '  }'. 
      '}'. 
      'function checkRegexp( o, regexp, box, n ) {'. 
      '  if ( !( regexp.test( o.val() ) ) ) {'. 
      '    o.addClass( "ui-state-error" );'. 
      '    updateTips( box, n );'. 
      '    return false;'. 
      '  } else {'. 
      '    return true;'. 
      '  }'. 
      '}'. 
      'function displayDate(disp, val) {'. 
      '  var months = new Array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");'. 
      '  date = val.val().split("/");'. 
      '  month = parseInt(date[0], 10);'. 
      '  disp.text(months[month]+" "+date[1]+", "+date[2]);'. 
      '}'; 
    $this->content =  
      ' </script>'. 
      '</head>'. 
      '<body>'; 
    $this->content .= ''. 
      ' <input type="text" name="game" id="game" class="text" />'. 
      ' <button id="find">Find</button>'. 
      '<table id="gameTable" class="tablesorter">'. 
      ' <thead><tr><th>Game</th>'; 
    foreach($this->stores as $store) $this->content .= '<th>'.$store->name.'</th>'; 
    $this->content .= '</tr></thead><tbody id="gameBody">'. 
      '</tbody></table>'. 
      ''; 
    $this->jQuery .= ''. 
      '$("#find").button();'. 
      '$("#find").click(function() {submitFind();});'. 
      '$("#gameTable").tablesorter({widgets: ["zebra", "repeatHeaders"]});'. 
      '$("#game").keypress(function(event) {'. 
      '  if(event.which == 13) submitFind();'. 
      '});'. 
      'function submitFind() {'. 
      '  blockUI();'. 
      '  $.ajax({'. 
      '    type: "POST",'. 
      '    url: "'.wR.'index.php",'. 
      '    data: "task=find&game="+$("#game").val(),'. 
      '    success: function(msg) {'. 
      '      $("#gameBody").html(msg);'. 
      '      $("#gameTable").tablesorter({widgets: ["zebra", "repeatHeaders"]});'. 
      '      $("#gameTable").trigger("update");'. 
      '      unblockUI();'. 
      '    },'. 
      '    error: function() {'. 
      '      alert("Failed to submit search.");'. 
      '      unblockUI();'. 
      '    }'. 
      '  });'. 
      '}'; 
    $this->footer .= '</body>'. 
      '</html>'; 
  } 
  function display() { 
    echo $this->header."\n"; 
    echo $this->jQueryHeader."\n"; 
    echo $this->jQuery."\n"; 
    echo $this->jQueryFooter."\n"; 
    echo $this->java."\n"; 
    echo $this->content."\n"; 
    echo $this->footer."\n"; 
  } 
  /* 
   * Tasks 
   */ 
  function findTask() { 
    $html = '<tr><td>&nbsp;</td>'; 
    $allGames = array(); 
    $games = array(); 
    foreach($this->stores as $sid => $store) { 
      $games[$sid] = $store->search($_REQUEST['game']); 
      $html .= '<td>'.$games[$sid]['info'].'</td>'; 
      foreach($games[$sid] as $game => $gameInfo) { 
        $tPrice = str_replace("$", "", $gameInfo['salePrice']); 
        if(!(isset($allGames[$game]))) $allGames[$game] = $tPrice; 
        else { 
          if($tPrice{0} == '0') continue; 
          if($allGames[$game]{0} == '0') $allGames[$game] = $tPrice; 
          else if($tPrice < $allGames[$game]) $allGames[$game] = $tPrice; 
        } 
      } 
    } 
    $html .= '</tr>'; 
    ksort($allGames); 
    foreach($allGames as $game => $bestPrice) { 
      if($game == 'info') continue; 
      $html .= '<tr><td>'.$game.'</td>'; 
      foreach($this->stores as $sid => $store) { 
        if(isset($games[$sid][$game])) { 
          if(str_replace("$", "", $games[$sid][$game]['salePrice']) == $bestPrice) $html .= '<td style="background-color: #ddffdd">'; 
          else $html .= '<td>'; 
          if($games[$sid][$game]['price'] == $games[$sid][$game]['salePrice']) { 
            $html .= $games[$sid][$game]['price'].'<br />'; 
          } else { 
            $html .= '<font style="text-decoration: line-through;">'.$games[$sid][$game]['price'].'</font><br />'. 
              $games[$sid][$game]['salePrice'].'<br />'; 
          } 
          $html .= 'Stock: '.$games[$sid][$game]['stock']; 
          $html .= '</td>'; 
        } else { 
          $html .= '<td>&nbsp;</td>'; 
        } 
      } 
      $html .= '</tr>'; 
    } 
    die($html); 
  } 
} 
$page = new template(); 
$page->display(); 
?>