
<?php
 

function curl_multi_fetch($urlarr=array(),$itemid,$token){
 
  $result=$res=$ch=array();
  $nch = 0;
  $mh = curl_multi_init();
  foreach ($urlarr as $nk => $url) {
 
    $timeout=20;
    $ch[$nch] = curl_init();
    curl_setopt_array($ch[$nch], array(
    CURLOPT_URL => $url,
    CURLOPT_TIMEOUT => $timeout,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
     CURLOPT_CUSTOMREQUEST => "PATCH",
    CURLOPT_POSTFIELDS =>"{\n  \"name\": \"".$itemid."\"\n}",
    CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer ".$token,
    "Content-Type: application/json"
  ),
    
    ));
 
    curl_multi_add_handle($mh, $ch[$nch]);
    ++$nch;
 
  }
 
  /* wait for performing request */
 
  do {
    $mrc = curl_multi_exec($mh, $running);
  } while (CURLM_CALL_MULTI_PERFORM == $mrc);
 
  while ($running && $mrc == CURLM_OK) {
    // wait for network
    if (curl_multi_select($mh, 0.5) > -1) {
      // pull in new data;
      do {
        $mrc = curl_multi_exec($mh, $running);
      } while (CURLM_CALL_MULTI_PERFORM == $mrc);
    }
 
  }
 
  if ($mrc != CURLM_OK) {
    error_log("CURL Data Error");
  }
 
  /* get data */
 
  $nch = 0;
 
  foreach ($urlarr as $moudle=>$node) {
    if (($err = curl_error($ch[$nch])) == '') {
      $res[$nch]=curl_multi_getcontent($ch[$nch]);
      $result[$moudle]=$res[$nch];
    }else{
      error_log("curl error");
 
    }
 
    curl_multi_remove_handle($mh,$ch[$nch]);
    curl_close($ch[$nch]);
    ++$nch;
  }
 
  curl_multi_close($mh);
  echo "批量处理完成";
  return $result;
 
}
 


