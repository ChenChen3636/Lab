<?php
$date = new DateTime();
//echo $date->getTimestamp();
# 檢查檔案是否上傳成功
if ($_FILES['my_file']['error'] === UPLOAD_ERR_OK){

  #更改檔名
  $filename = $_FILES['my_file']['name'];
  $filename = $date->getTimestamp()."_".$filename;
  $file_mkdir_path =  substr($filename,0,-5);
  //echo $file_mkdir_path .'<br>';

  // echo '檔案名稱: ' . $_FILES['my_file']['name'] . '<br/>';
  //echo '檔案類型: ' . $_FILES['my_file']['type'] . '<br/>';
  // echo '檔案大小: ' . ($_FILES['my_file']['size'] / 1024) . ' KB<br/>';
  // echo '暫存名稱: ' . $_FILES['my_file']['tmp_name'] . '<br/>';

  # 檢查檔案是否已經存在
  if (file_exists('/PCAP_DB/user_upload/' . $file_mkdir_path.'/')){
    echo '檔案已存在。<br/>';
  } else {
    $file = $_FILES['my_file']['tmp_name'];

    $mkdir_command = "mkdir /PCAP_DB/user_upload/".$file_mkdir_path."/";
    shell_exec($mkdir_command);

    $dest = "/PCAP_DB/user_upload/". $file_mkdir_path ."/".$filename;
    # 將檔案移至指定位置
    move_uploaded_file($file, $dest);
  }

  $cp_command = "cp -Rf /PCAP_DB/Upload/* /PCAP_DB/user_upload/".$file_mkdir_path."/";
  shell_exec($cp_command);

  header("Location: ./flashball.php");
}
else {
  echo '錯誤代碼：' . $_FILES['my_file']['error'] . '<br/>';
}


?>