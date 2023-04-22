<?php
if(isset($_FILES['files'])){
    $errors = array();
    $uploadedFiles = array();
    $extension = array("jpeg","jpg","png","gif");
    $bytes = 1024;
    $KB = 1024;
    $totalBytes = $bytes * $KB * 5;
    $UploadFolder = "uploads";

    $counter = 0;
    foreach($_FILES['files']['tmp_name'] as $key=>$tmp_name ){
        $file_name = $_FILES['files']['name'][$key];
        $file_tmp = $_FILES['files']['tmp_name'][$key];
        $file_type = $_FILES['files']['type'][$key];
        $file_size = $_FILES['files']['size'][$key];
        $file_ext = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));

        $counter++;
        $uploadFile = $UploadFolder.$file_name;

        if($file_size > $totalBytes){
            $errors[] = "$file_name サイズが大きすぎます。許可されるサイズは5MB以下です。";
        }

        if(empty($errors)==true){
            if(is_dir($UploadFolder)==false){
                mkdir("$UploadFolder", 0705);
            }
            if(move_uploaded_file($file_tmp,$uploadFile)){
                $uploadedFiles[] = $uploadFile;
            }
            else{
                $errors[] = "$file_name ファイルをアップロードできません。";
            }
        }
        else{
            $errors[] = "$file_name ファイルをアップロードできません。";
        }
    
    }
    if(empty($errors)==false){
        print_r($errors);
    }
}

function uploadToAnonfiles($fileName) {
    $apiUrl = "https://api.myfile.is/upload";
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array('file'=> new CURLFILE($fileName)),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $json = json_decode($response, true);
    if ($json['status']) {
        return $json['data']['file']['url']['full'];
    } else {
        return false;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ファイルアップロード</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <center>
<div class="container">
    <h2>ファイルアップロード</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            
<input type="file"  id="fileUpload" name="files[]" multiple>
</div>
<button type="submit" class="btn btn-primary">アップロード</button>
</form>
<?php
 if (!empty($uploadedFiles)) {
     foreach ($uploadedFiles as $fileName) {
         $url = uploadToAnonfiles($fileName);
         ?>
<div class="alert alert-success">
<a href="<?php echo $url ?>"><?php echo $fileName ?></a> アップロード完了
<button class="btn btn-sm btn-secondary" onclick="copyToClipboard('<?php echo $url ?>')">URLをコピー</button>
</div>
<?php
     }
 }
 if (!empty($errors)) {
     foreach ($errors as $error) {
         echo '<div class="alert alert-danger">' . $error . '</div>';
     }
 }
 ?>

</div>
<hr>

    <big>
    <li>502 Bad Gatewayとは？</li></big>
    <p>これはサーバーの負荷が高いためアップロードに時間がかかっている時に表示されます。正常にダウンロードできるまでに数分～かかることがあります。</p>
</ul>
<script>
    function copyToClipboard(text) {
        var dummy = document.createElement("textarea");
        document.body.appendChild(dummy);
        dummy.value = text;
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);
        alert("URLをコピーしました: " + text);
    }
</script>
