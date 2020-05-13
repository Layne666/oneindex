<?php 
        if ($item["size"] < 10000000) {
                $url = 'https://view.officeapps.live.com/op/view.aspx?src='.urlencode($item['downloadUrl']);
                view::direct($url);
        } else {
                view::direct($item['downloadUrl']);
        }
        exit();
?>
