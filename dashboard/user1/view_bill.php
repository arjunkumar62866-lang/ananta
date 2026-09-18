<?php @session_start();
// if (!isset($_SESSION["franchiseeid"])) { header("Location:login");} 
include "common/connection.php";
include("connection.php");
include "common/db_method.php";
// $auserid= $_SESSION['auserid'];

 
//  $sqlad="select * from admin where auserid='$auserid'";
// $resultad=mysqli_query($db,$sqlad);
//  if(mysqli_num_rows($resultad)>0);
//  while($rowad = mysqli_fetch_assoc($resultad)) {
//  if($resultad){
// $nameadmin = $rowad["name"]; 
// $nameadmin = $rowad["auserid"]; 
//  }}
 
$t_id = $_GET['tid'];
 
 $homeset= getHomeSettings($pdo);
$hmmobile = $homeset['mobile'];
$hmemail = $homeset['email'];
$hmaddress = $homeset['address'];
$hmtitle = $homeset['title'];
$hmurl = $homeset['url'];
$hmpackage = $homeset['package'];
$hmpre = $homeset['pre'];
$hmwebsite = $homeset['website'];
$hmgst_no = $homeset['gst_no'];
// $hmstate_code = $homeset['state_code'];
$hmbitly = $homeset['bitly'];
$hmemailfrom = $homeset['emailfrom'];
$hmbg = $homeset['background'];
$hmlogo = $homeset['logo'];
$hmfavicon = $homeset['favicon'];
$hmcolor = $homeset['color'];


$stmt = $pdo->prepare("SELECT * FROM tbl_order WHERE tr_id = :t_id");
$stmt->execute([':t_id' => $t_id]);
    if($stmt->rowCount() === 1);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $tr_id=$row['tr_id'];
    $franchiseeid=$row['franchiseeid'];
     $name=$row['name'];
      $email=$row['email'];
       $city=$row['city'];
        $state=$row['state'];
         $pincode=$row['pincode'];
          $mobile=$row['mobile'];
           $pro_title=$row['pro_title'];
            $pro_code=$row['pro_code'];
             $pro_price=$row['pro_price'];
              $dp_price=$row['dp_price'];
               $sp_price=$row['sp_price'];
                $total_price=$row['total_price'];
                 $quantity=$row['quantity'];
                 $date=$row['date'];
                  $payment_id=$row['payment_id'];
                   $mode=$row['mode']; 
                   $subject=$row['subject'];
                   $amount=$row['amount'];
                   $remark=$row['remark'];
                   $pay_date=$row['pay_date'];
                   $pay_time=$row['pay_time'];
                   $userid=$row['userid'];
                   $wallet=$row['wallet_type'];
                   $address=$row['address'];
                     $utrCode=$row['utrCode'];
                 $depositor=$row['depositor'];
     function convertNumberToWords($number) {
    $hyphen = '-';
    $conjunction = ' and ';
    $separator = ', ';
    $negative = 'negative ';
    $decimal = ' point ';
    $dictionary = [
        0 => 'Zero',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
        20 => 'Twenty',
        30 => 'Thirty',
        40 => 'Forty',
        50 => 'Fifty',
        60 => 'Sixty',
        70 => 'Seventy',
        80 => 'Eighty',
        90 => 'Ninety',
        100 => 'Hundred',
        1000 => 'Thousand',
        100000 => 'Lakh',
        10000000 => 'Crore'
    ];

    if (!is_numeric($number)) {
        return false;
    }

    if ($number < 0) {
        return $negative . convertNumberToWords(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convertNumberToWords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convertNumberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convertNumberToWords($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = [];
        foreach (str_split((string) $fraction) as $digit) {
            $words[] = $dictionary[$digit];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}
     
     
     
     
     $userdata=getuserdatabysponserid($userid);
     $sponsername=$userdata["sponsername"];
      $sponserid=$userdata["sponserid"];
             
?>



<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="<?php echo $hmtitle; ?>">
<link rel="icon" type="image/x-icon" href="<?php echo "../../img/".$hmlogo;?>">
<title><?php echo $hmtitle;?></title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<style>@import url(#);*,::after,::before{-webkit-box-sizing:border-box;box-sizing:border-box}
html{line-height:1.15;-webkit-text-size-adjust:100%}
body{margin:0}
a{background-color:transparent}
b{font-weight:bolder}
img{border-style:none}
button{font-family:inherit;font-size:100%;line-height:1.15;margin:0}
button{overflow:visible}
button{text-transform:none}
button{-webkit-appearance:button}
button::-moz-focus-inner{border-style:none;padding:0}
button:-moz-focusring{outline:1px dotted ButtonText}
::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}
body,html{color:#333;font-family:'Inter',sans-serif;font-size:13px;font-weight:400;line-height:1.5em;overflow-x:hidden;background-color:#f5f7ff}
p,div{margin-top:0;line-height:1.5em}
p{margin-bottom:15px}
img{border:0;max-width:40%;height:auto;margin-bottom: 20px;}
a{color:inherit;text-decoration:none;-webkit-transition:all .3s ease;transition:all .3s ease}
a:hover{color:#2ad19d}
button{color:inherit;-webkit-transition:all .3s ease;transition:all .3s ease}
a:hover{text-decoration:none;color:inherit}
table{width:100%;caption-side:bottom;border-collapse:collapse}
th{text-align:left}
td{border-top:1px solid #eaeaea}
td,th{padding:10px 15px;line-height:1.55em}
b{font-weight:bold}
.cs-f16{font-size:16px}
.cs-semi_bold{font-weight:600}
.cs-bold{font-weight:700}
.cs-m0{margin:0}
.cs-mb0{margin-bottom:0}
.cs-mb5{margin-bottom:5px}
.cs-mb10{margin-bottom:10px}
.cs-mb25{margin-bottom:25px}
.cs-width_1{width:8.33333333%}
.cs-width_2{width:16.66666667%}
.cs-width_3{width:25%}
.cs-width_4{width:33.33333333%}
.cs-primary_color{color:#fff}
.cs-focus_bg{background:#fe0303;}
.cs-container{max-width:880px;padding:30px 15px;margin-left:auto;margin-right:auto}
.cs-text_right{text-align:left}.cs-border_top_0{border-top:0}
.cs-border_top{border-top:1px solid #eaeaea}
.cs-border_left{border-left:1px solid #eaeaea}
.cs-round_border{border:1px solid #eaeaea;overflow:hidden;border-radius:6px}
.cs-border_none{border:none}.cs-invoice.cs-style1{background:#fff;border-radius:10px;padding:20px}
.cs-invoice.cs-style1 .cs-invoice_head{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between}
.cs-invoice.cs-style1 .cs-invoice_head.cs-type1{-webkit-box-align:end;-ms-flex-align:end;align-items:flex-end;padding-bottom:25px;border-bottom:1px solid #eaeaea}

.cs-invoice.cs-style1 .cs-invoice_footer{display:-webkit-box;display:-ms-flexbox;display:flex}
.cs-invoice.cs-style1 .cs-invoice_footer table{margin-top:-1px}
.cs-invoice.cs-style1 .cs-left_footer{width:55%;padding:10px 15px}
.cs-invoice.cs-style1 .cs-right_footer{width:46%}
.cs-invoice.cs-style1 .cs-note{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:start;-ms-flex-align:start;align-items:flex-start;margin-top:40px}
.cs-invoice.cs-style1 .cs-note_left{margin-right:10px;margin-top:6px;margin-left:-5px;display:-webkit-box;display:-ms-flexbox;display:flex}
.cs-invoice.cs-style1 .cs-note_left svg{width:0px}
.cs-invoice.cs-style1 .cs-invoice_left{max-width:25%; matgin-left:25px}
.cs-invoice_btns{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;margin-top:30px}
.cs-invoice_btns .cs-invoice_btn:first-child{border-radius:5px 0 0 5px}
.cs-invoice_btns .cs-invoice_btn:last-child{border-radius:0 5px 5px 0}
.cs-invoice_btn{display:-webkit-inline-box;display:-ms-inline-flexbox;display:inline-flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;border:none;font-weight:600;padding:8px 20px;cursor:pointer}.cs-invoice_btn svg{width:24px;margin-right:5px}.cs-invoice_btn.cs-color1{color:#111;background:rgba(42,209,157,.15)}
.cs-invoice_btn.cs-color1:hover{background-color:rgba(42,209,157,.3)}
.cs-invoice_btn.cs-color2{color:#fff;background:#2ad19d}
.cs-invoice_btn.cs-color2:hover{background-color:rgba(42,209,157,.8)}
.cs-table_responsive{overflow-x:hidden}.cs-table_responsive>table{width:100%}
.cs-bar_list li:not(:last-child) {margin-bottom:10px}

.cs-table.cs-style2 tr:not(:first-child) {border-top:1px dashed #eaeaea}
.cs-list.cs-style1 li:not(:last-child) {border-bottom:1px dashed #eaeaea}

.cs-table.cs-style1 .cs-table.cs-style1 tr:not(:first-child) td {border-color:#eaeaea}

@media (max-width:767px)
{.cs-mobile_hide{display:none}
	.cs-invoice.cs-style1{padding:30px 20px}
	cs-invoice.cs-style1 .cs-right_footer
	{width:100%}}
	@media (max-width:500px){
		.cs-invoice.cs-style1 .cs-logo{margin-bottom:10px}
		.cs-invoice.cs-style1 .cs-invoice_head{-webkit-box-orient:vertical;-webkit-box-direction:normal;-ms-flex-direction:column;flex-direction:column}
	.cs-invoice.cs-style1 .cs-invoice_head.cs-type1{-webkit-box-orient:vertical;-webkit-box-direction:reverse;-ms-flex-direction:column-reverse;flex-direction:column-reverse;-webkit-box-align:center;-ms-flex-align:center;align-items:center;text-align:center}
.cs-invoice.cs-style1 .cs-invoice_head .cs-text_right{text-align:left}.cs-invoice.cs-style1 .cs-invoice_left{max-width:100%}}
.cs-text{
	margin: 0px 200px 0px 0px;
}
.table-responsive table, tr,th,td{
    background:none!important;
    border:none!important;
}
.list-group-flush li{
    border:none!important;
    background:none!important;
}
.list-group-flush{
    display:flex;
}

body{
    background:#fff!important;
}

</style>

<style>
  th{
      padding:0px!important;
  }
  .border {
    border: 1px solid #000 !important;
  }
  @media print {
    body {
      -webkit-print-color-adjust: exact;
    }

    .container-new {
      max-height: 100vh;
      overflow: hidden;
    }

    img {
      max-width: 80px; 
    }
  }
  @media (max-width: 768px) {
    h3, .container-new, .table, .list-group-item {
      font-size: 0.8em;
    }
    th, td {
      font-size: 0.7em;
      padding: 1px 2px;
    }
  }
 th,td{
      font-size: 0.9rem;
  font-weight: 600; 
  /*padding: 4px 6px; */
  text-align: left;
  vertical-align: top;
 }
 .table p {
  margin: 0;  
  padding: 0; 
  line-height: 1.2; 
}
.table tr{
     margin: 0;  
  padding: 0; 
  line-height: 1.2; 
}


</style>


<script data-pagespeed-no-defer>//<![CDATA[
(function(){function f(a,b,d){if(a.addEventListener)a.addEventListener(b,d,!1);else if(a.attachEvent)a.attachEvent("on"+b,d);else{var c=a["on"+b];a["on"+b]=function(){d.call(this);c&&c.call(this)}}};window.pagespeed=window.pagespeed||{};var g=window.pagespeed;function k(a){this.g=[];this.f=0;this.h=!1;this.j=a;this.i=null;this.l=0;this.b=!1;this.a=0}function l(a,b){var d=b.getAttribute("data-pagespeed-lazy-position");if(d)return parseInt(d,0);var d=b.offsetTop,c=b.offsetParent;c&&(d+=l(a,c));d=Math.max(d,0);b.setAttribute("data-pagespeed-lazy-position",d);return d}
function m(a,b){var d,c,e;if(!a.b&&(0==b.offsetHeight||0==b.offsetWidth))return!1;a:if(b.currentStyle)c=b.currentStyle.position;else{if(document.defaultView&&document.defaultView.getComputedStyle&&(c=document.defaultView.getComputedStyle(b,null))){c=c.getPropertyValue("position");break a}c=b.style&&b.style.position?b.style.position:""}if("relative"==c)return!0;e=0;"number"==typeof window.pageYOffset?e=window.pageYOffset:document.body&&document.body.scrollTop?e=document.body.scrollTop:document.documentElement&&
document.documentElement.scrollTop&&(e=document.documentElement.scrollTop);d=window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight;c=e;e+=d;var h=b.getBoundingClientRect();h?(e=h.top-d,c=h.bottom):(h=l(a,b),d=h+b.offsetHeight,e=h-e,c=d-c);return e<=a.f&&0<=c+a.f}
k.prototype.m=function(a){p(a);var b=this;window.setTimeout(function(){var d=a.getAttribute("data-pagespeed-lazy-src");if(d)if((b.h||m(b,a))&&-1!=a.src.indexOf(b.j)){var c=a.parentNode,e=a.nextSibling;c&&c.removeChild(a);a.c&&(a.getAttribute=a.c);a.removeAttribute("onload");a.tagName&&"IMG"==a.tagName&&g.CriticalImages&&f(a,"load",function(){g.CriticalImages.checkImageForCriticality(this);b.b&&(b.a--,b.a||g.CriticalImages.checkCriticalImages())});a.removeAttribute("data-pagespeed-lazy-src");a.removeAttribute("data-pagespeed-lazy-replaced-functions");
c&&c.insertBefore(a,e);if(c=a.getAttribute("data-pagespeed-lazy-srcset"))a.srcset=c,a.removeAttribute("data-pagespeed-lazy-srcset");a.src=d}else b.g.push(a)},0)};k.prototype.loadIfVisibleAndMaybeBeacon=k.prototype.m;k.prototype.s=function(){this.h=!0;q(this)};k.prototype.loadAllImages=k.prototype.s;function q(a){var b=a.g,d=b.length;a.g=[];for(var c=0;c<d;++c)a.m(b[c])}function t(a,b){return a.a?null!=a.a(b):null!=a.getAttribute(b)}
k.prototype.u=function(){for(var a=document.getElementsByTagName("img"),b=0,d;d=a[b];b++)t(d,"data-pagespeed-lazy-src")&&p(d)};k.prototype.overrideAttributeFunctions=k.prototype.u;function p(a){t(a,"data-pagespeed-lazy-replaced-functions")||(a.c=a.getAttribute,a.getAttribute=function(a){"src"==a.toLowerCase()&&t(this,"data-pagespeed-lazy-src")&&(a="data-pagespeed-lazy-src");return this.c(a)},a.setAttribute("data-pagespeed-lazy-replaced-functions","1"))}
g.o=function(a,b){function d(){if(!(c.b&&a||c.i)){var b=200;200<(new Date).getTime()-c.l&&(b=0);c.i=window.setTimeout(function(){c.l=(new Date).getTime();q(c);c.i=null},b)}}var c=new k(b);g.lazyLoadImages=c;f(window,"load",function(){c.b=!0;c.h=a;c.f=200;if(g.CriticalImages){for(var b=0,d=document.getElementsByTagName("img"),r=0,n;n=d[r];r++)-1!=n.src.indexOf(c.j)&&t(n,"data-pagespeed-lazy-src")&&b++;c.a=b;c.a||g.CriticalImages.checkCriticalImages()}q(c)});b.indexOf("data")&&((new Image).src=b);f(window,
"scroll",d);f(window,"resize",d)};g.lazyLoadInit=g.o;})();

pagespeed.lazyLoadInit(true, "../../../pagespeed_static/1.JiBnMqyl6S.gif");

//]]></script></head>

<body >
    <form name="form1" method="post" action="view_bill?id=<?php echo $t_id;  ?>" id="form1">
<div>
<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="" />
</div>


<div  class="container-new">
   <div style="height:96px;" class="row  align-items-center">
    <div class="col-lg-3 col-4">
        <img style="width:100%;" src="<?php echo "../../img/".$hmlogo;?>" alt="logo" class="logo">
    </div>
    <div class="col-lg-9 col-8 text">
        <h3 class="company-title">French Life Care International Pvt. Ltd.</h3>
        <p class="company-details">
            <?php echo "Badi Bihar, Parwana Nagar, Ivri Road, Near Radhe Radhe Mandir,<br>Izzat Nagar, Bareilly Uttar Pradesh India - 243001" ?><br>
            Phone: +91<?php echo $hmmobile ?><br>
            Email: <?php echo $hmemail ?>
        </p>
    </div>
</div>

    
    <!--table content start-->
    
    <div class="row ">
       <div  class="col-lg-12 mx-auto ">
           <div style="border-bottom:none!important;" class="table-responsive border">
               <table style="border:none!important;background:transparent!important;" class="table">
  <thead>
    <tr>
      <th scope="col">GSTIN : <?php  echo $hmgst; ?></th>
      <th scope="col">TAX INVOICE</th>
      <th scope="col">Original For Recipient</th>
     
    </tr>
  </thead>
</table>
           </div>
        </div>
        
        
        <div class="col-lg-12 ">
             <div class="table-responsive border">
                 <table class="table">
  <thead>
    <tr class="newpadding">
      <th colspan="3">Invoice No.</th>
      <th colspan="3">: <?php  echo $tr_id; ?></th>
      <td colspan="3">Shipping Company</td>
      <th>:</th>
    </tr>
      <tr class="newpadding">
      <th colspan="3">Invoice Date .</th>
      <th colspan="3">: <?php echo $pay_date;?></th>
      <td colspan="3">Vehicle No</td>
      <th>:</th>
    </tr>
      <tr class="newpadding">
      <td colspan="3">Place of Supply.</td>
      <td colspan="3">: Uttar Pradesh</td>
      <td colspan="3">Bill Type</td>
      <th>:</th>
      <td>Credit</td>
    </tr>
      <tr class="newpadding">
      <td colspan="3">Station.</td>
      <td colspan="3">: Bareilly</td>
      <td colspan="3">Distance</td>
      <th>:</th>
    </tr>
    
     <tr class="newpadding">
      <td colspan="3">UTR CODE</td>
      <td colspan="5">: <?php echo $utrCode; ?></td>
      <th></th>
      <th>:</th>
    </tr>
    <!--<hr>-->
    <tr style="border-left:none!important;border-right:none!important;" class="border">
        <th style="border-right:1px solid black!important;"   colspan="7">Details of Receiver / Bill To
        </th>
        <th colspan="7">Details of Consignee / Ship To</th>
    </tr>

    <tr style="border-left:none!important;border-right:none!important;" class="border">
        <th colspan="7" style="border-right:1px solid black!important;">Customer Id: <?php echo $userid;?><p>Name : <?php echo $name;?></p><p>Address : <?php echo $address;?></p><p>GSTIN / UIN :</p><p>Phone : <?php echo $mobile;?></p><p>State Code : <?php echo $state; ?></p></th>
        
        <th colspan="7" >Name : <?php echo $name;?><p>Address : <?php echo $address;?></p><p>GSTIN / UIN :</p><p>Phone : <?php echo $mobile;?></p><p>State Code : <?php echo $state; ?></p></th>
    </tr>
    <tr style="border-left:none!important;border-right:none!important;" class="border">
        <th colspan="5 "></th>
        <th class="border">Discount</th>
        <th class="border">Taxable</th>
        <?php if($state=='Uttar Pradesh'){ ?>
        <th class="border text-center" colspan="2">CGST</th>
        <th class="border text-center"  colspan="2">SGST</th>
        <?php } else { ?>
        <th class="border text-center"  colspan="2">IGST</th>
        <?php }?>
        <th style="border-left:none!important;" class="border">Total</th>
    </tr>
    
    
    
    <tr style="border-left:none!important;border-right:none!important;" class="border">
        <th style="border-left:none!important;border-right:none!important;" class="border">S No</th>
        <th  class="border">Description</th>
        <th  class="border">HSn/SAC</th>
        <th  class="border">QTY</th>
        <th  class="border">Item Rate</th>
        <th  class="border">Amount</th>
        <th  class="border">Value</th>
        <?php if($state=='Uttar Pradesh'){ ?>
        <th  class="border">Rate</th>
        <th  class="border">Amount</th>
        <th  class="border">Rate</th>
        <th  class="border">Amount</th>
        <?php }else {?>
        <th  class="border">Rate</th>
        <th  class="border">Amount</th>
        <?php }?>
        <th style="border-left:none!important;"  class="border">Amount</th>
    </tr>
    
    
    
  <?php

$stmt = $pdo->prepare("
    SELECT * 
    FROM tbl_transaction_details 
    LEFT JOIN tbl_product 
    ON tbl_product.id = tbl_transaction_details.pro_id 
    WHERE tbl_transaction_details.tr_id = :t_id
");
$stmt->execute([':t_id' => $t_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize variables as integers
$i = 1;
$totalqty = 0;
$totalAmount = 0;
$prodpprice = 0;
$totaltaxableamount = 0;
$amount1 = 0;
$amount2 = (float)0;
$amount3 = (float)0;
$igstsummary = 0;
$newigst_pricesummary = 0;
$newgst_pricesummary = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $new = $i++;
    $pcategory = $row['main_cat'];
    $pname = $row['pro_name'];
    $ppro_code = $row['pro_code'];
    $dp_coast = $row['pro_price'];
    $pprice = $row['pro_dp_price'];
    $oqty = (float)($row['pro_qty']);
    $igst = (float)($row['gst_rate']); //gst price
    $pro_bv_price = $row['pro_bv_price'];
    $pro_mrp_price = $row['pro_mrp_price'];
    $invoicedata = $row['date'];
    
    $totalAmt = (float)($row['pro_mrp_price']);
    $gst_price = (float)($row['pro_gst']); //gst percentage
    $totalAmt=(float)$pro_mrp_price+(float)$igst;
    
    $MRP = $oqty * intval($dp_coast);
    $newigst_price = $oqty * $gst_price;
    
    
    $igstsummary += $igst;
    $newigst_pricesummary += $newigst_price;
    $newgst_price = $newigst_price / 2;
    $newgst_pricesummary += $newgst_price;
    $psgst = $igst / 2;
    $FinalAmt = $MRP + $igst;
    
    // Update total calculations
    $totalqty += (float)$oqty;
    $totalAmount += (float)$totalAmt;
    $prodpprice += (float)$pprice;
    $totaltaxableamount += (float)($row['pro_mrp_price']);;
    $amount1 += (float)$psgst;
    $amount2 += (float)$psgst;
    $amount3 += (float)$igst;
    $productdata=getproductCode($ppro_code);
    $hsncode=$productdata['hsn'];
?>
<tr style="border-left:none!important;border-right:none!important;" class="border">
    <td style="border-left:none!important;border-right:none!important;" class="border"><?php echo $new; ?></td>
    <td class="border"><?php echo $pname; ?></td>
    <td class="border"><?php echo $hsncode; ?></td>
    <td class="border"><?php echo $oqty; ?></td>
    <td class="border"><?php echo $totalAmt; ?></td>
    <td class="border">0</td>
    
    
    <td class="border"><?php echo intval($row['pro_mrp_price']); ?></td>
    
     <?php if($state=='Uttar Pradesh'){ ?>
    
    <td class="border"><?php echo $gst_price; ?></td>
    <td class="border"><?php echo $psgst; ?></td>
    <td class="border"><?php echo $gst_price; ?></td>
    <td class="border"><?php echo $psgst; ?></td>
    
    <?php }else{?>
    <td class="border"><?php echo $newigst_price; ?></td>
    <td class="border"><?php echo $igst; ?></td>
    <?php }?>
    <td style="border-left:none!important;" class="border"><?php echo $totalAmt ?></td>
</tr>
<?php } ?>

<tr style="border-left:none!important;border-right:none!important;" class="border">
   <th colspan="2"></th>
   <th class="border">Total</th>
   <th class="border"><?php echo $totalqty; ?></th>
   <th class="border"><?php echo $totalAmount; ?></th>
   <th class="border"><?php echo $prodpprice; ?></th>
   <th class="border"><?php echo $totaltaxableamount; ?></th>
   <?php if($state=='Uttar Pradesh'){ ?>
   <th class="border"></th>
   <th class="border"><?php echo $amount1; ?></th>
   <th class="border"></th>
   <th class="border"><?php echo $amount2; ?></th>
   <?php }else{?>
   <th class="border"></th>
   <th class="border"><?php echo $amount3; ?></th>
   <?php }?>
   <th class="border"><?php echo $totalAmount; ?></th>
</tr>

    
    
    <tr style="border-left:none!important;border-right:none!important;" class="border">
        <td style="border-left:none!important;border-right:none!important;padding:0px!important;vertical-align: top;" class="border" colspan="9" >यह कोई धन वितरण, चिट फंड, या पैसा दुगुना, तिगुना करने की स्कीम नही है, आप अपने पैसे का मूल्य का अपनी इच्छानुसार प्रोडेक्ट खरीद रहे हैं बाद में माल/पैसे वापस नही होंगे। काम करोगे तो ही पैसा आयेगा।.<p style="border-top:1px solid black!important;border-left:none!important;border-right:none!important;">Rupees in words : <?php echo "INR " . convertNumberToWords($totalAmount) . " Only"; ?></p></td>
        <td style="border-right:none!important;" rowspan="1" colspan="5" class="border">
            
               <ul class="list-group list-group-flush">
              <li style="display:table;" class="list-group-item"><h6 class="text-left">Sub Total</h6> <span style="display:table-cell;text-align:right;"><?php echo $totalAmount ?></span></li>
               <li style="display:table;" class="list-group-item"><h6 class="text-left">Taxable Amount</h6> <span style="display:table-cell;text-align:right;"><?php echo $totaltaxableamount; ?></span></li>
               <?php if($state=='Uttar Pradesh'){ ?>
                <li style="display:table;" class="list-group-item"><h6 class="text-left">CGST</h6> <span style="display:table-cell;text-align:right;"><?php echo $amount1; ?></span></li>
                
                  <li style="display:table;" class="list-group-item"><h6 class="text-left">SGST/UTGST</h6> <span style="display:table-cell;text-align:right;"><?php echo $amount1; ?></span></li>
                  <?php }else{?>
                   <li style="display:table;" class="list-group-item"><h6 class="text-left">IGST</h6> <span style="display:table-cell;text-align:right;"><?php echo $amount3 ?></span></li>
                   <?php }?>
                  
                  <li style="display:table;" class="list-group-item"><h6 class="text-left">Round Off</h6> <span style="display:table-cell;text-align:right;">0</span></li>
                   <li style="display:table;" class="list-group-item"><h6 class="text-left">Bill Total</h6> <span style="display:table-cell;text-align:right;"><?php echo $totalAmount ?></span></li>
              
            </ul>
            
        </td>
    </tr>
    
    <tr style="border-left:none!important;border-right:none!important;" class="border">
        <th colspan="7">
            <p>Introducer Name :  <span> <?php echo $sponsername ?></span></p>
            <p>Depositor Name : <span> <?php  $userdata=getuserdatabysponserid($depositor);
     $name=$userdata["name"];
     echo $name;
       ?></span></p>
        </th>
        <th colspan="7">
            <p>ID No : <span><?php echo $sponserid; ?></span></p>
            <p>ID No : <span><?php echo $id=$userdata['userid']; ?></span></p>
        </th>
    </tr>
    
  
    
  </thead>
</table>

<table style="width: 100%; border-collapse: collapse;margin-top:-13px;">
    <tr>
        <th style="border-right: 1px solid black!important; text-align: left; width: 40%;margin-top:-21px;" rowspan="3">
            Bank Details
            <br><br>
        </th>
        <th style="border-right: 1px solid black!important; text-align: center; width: 20%;" rowspan="3">
            <div style="margin-top:55px;">(Common Seal)</div>
        </th>
        <th style="border: 1px solid black; padding: 10px; text-align: center; width: 40%;margin-top:-21px;" rowspan="3">
            For <?php echo $hmtitle; ?>
            <br><br>
            <div style="margin-top:21px;">Authorized Signature</div>
        </th>
    </tr>
</table>
             </div>
        </div>
        
        
    </div>
    
    
    <!--table content ends here-->
    
    
    
</div>




<script type="text/javascript">
//<![CDATA[
window.print();
// window.onfocus = function() { window.close(); }
//]]>
</script>

<div>

	<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="6D212CFA" />
</div>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<body>








</div>
<div class="cs-note">
<div class="cs-note_left">
<!--<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path d="M416 221.25V416a48 48 0 01-48 48H144a48 48 0 01-48-48V96a48 48 0 0148-48h98.75a32 32 0 0122.62 9.37l141.26 141.26a32 32 0 019.37 22.62z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><path d="M256 56v120a32 32 0 0032 32h120M176 288h160M176 368h160" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>-->
</div>

</div>
</div>

</div>
</div>
<script src="assets/js/jquery.min.js%2bjspdf.min.js.pagespeed.jc.PuH6JTVTsF.js"></script><script>eval(mod_pagespeed_29hrpuHQrF);</script>
<script>eval(mod_pagespeed_otZnIY0lOn);</script>
<script src="assets/js/html2canvas.min.js.pagespeed.ce.QR9HrITRH0.js"></script>
<script>//<![CDATA[
(function($){'use strict';$('#download_btn').on('click',function(){var downloadSection=$('#download_section');var cWidth=downloadSection.width();var cHeight=downloadSection.height();var topLeftMargin=40;var pdfWidth=cWidth+topLeftMargin*2;var pdfHeight=pdfWidth*1.5+topLeftMargin*2;var canvasImageWidth=cWidth;var canvasImageHeight=cHeight;var totalPDFPages=Math.ceil(cHeight/pdfHeight)-1;html2canvas(downloadSection[0],{allowTaint:true}).then(function(canvas){canvas.getContext('2d');var imgData=canvas.toDataURL('image/jpeg',1.0);var pdf=new jsPDF('p','pt',[pdfWidth,pdfHeight]);pdf.addImage(imgData,'JPG',topLeftMargin,topLeftMargin,canvasImageWidth,canvasImageHeight);for(var i=1;i<=totalPDFPages;i++){pdf.addPage(pdfWidth,pdfHeight);pdf.addImage(imgData,'JPG',topLeftMargin,-(pdfHeight*i)+topLeftMargin*0,canvasImageWidth,canvasImageHeight);}pdf.save('ivonne-invoice.html');});});})(jQuery);
//]]></script>
<noscript class="psa_add_styles"><link rel="stylesheet" href="assets/css/A.style.css.pagespeed.cf.GlNaA0J7L1.css"></noscript><script data-pagespeed-no-defer>//<![CDATA[
(function(){function b(){var a=window,c=e;if(a.addEventListener)a.addEventListener("load",c,!1);else if(a.attachEvent)a.attachEvent("onload",c);else{var d=a.onload;a.onload=function(){c.call(this);d&&d.call(this)}}};var f=!1;function e(){if(!f){f=!0;for(var a=document.getElementsByClassName("psa_add_styles"),c=0,d;d=a[c];++c)if("NOSCRIPT"==d.nodeName){var k=document.createElement("div");k.innerHTML=d.textContent;document.body.appendChild(k)}}}function g(){var a=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||window.oRequestAnimationFrame||window.msRequestAnimationFrame||null;a?a(function(){window.setTimeout(e,0)}):b()}
var h=["pagespeed","CriticalCssLoader","Run"],l=this;h[0]in l||!l.execScript||l.execScript("var "+h[0]);for(var m;h.length&&(m=h.shift());)h.length||void 0===g?l[m]?l=l[m]:l=l[m]={}:l[m]=g;})();
pagespeed.CriticalCssLoader.Run();
//]]></script></body>
</html> 