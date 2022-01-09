<?php
$strURL = '';
if(isset($_POST['submit']) && $_POST['scp'] == 1) {
class scraper {
   
    public function curlTheUrl($strURL){
        $strFileContent = '';
        $objCurl = curl_init($strURL);
        curl_setopt($objCurl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1;)');
        curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($objCurl, CURLOPT_FOLLOWLOCATION, 1);
        $strFileContent = curl_exec($objCurl);
        curl_close($objCurl);
        unset($objCurl);
        return $strFileContent;
    }
    
}

$scraper = new scraper();
$strURL = 'http://www.cimaglobal.com/About-us/Find-a-CIMA-Accountant/?surname=&company=&city=&county=&country=United+Kingdom&funcspecialism=&sectorspecialism=&sortby=mostrelevant&results=10#Results';
$strFileContent = $scraper->curlTheUrl($strURL);

$objDomDoc = new DOMDocument();
@$objDomDoc->loadHTML($strFileContent);
foreach($objDomDoc->getElementsByTagname('div') as $objDivs){
    if(trim($objDivs->getAttribute('id')) == 'ContentPlaceHolder1_PnlWrapper'){
        foreach($objDivs->getElementsByTagname('ul') as $objUls){
            if(trim($objUls->getAttribute('class')) == 'accountantListing'){
                $strCsvFiePath = 'save/ScrapedCSVFile.csv';
                if(file_exists($strCsvFiePath)){
                    unlink($strCsvFiePath);
                }
                $strCsvFile = fopen($strCsvFiePath, 'a');
                $strCsvData = 'Company Name'.','.'First Name'.','.'Email Id';
                fwrite($strCsvFile, $strCsvData.PHP_EOL);                
                foreach($objUls->getElementsByTagname('li') as $objLis){
                    foreach($objLis->getElementsByTagname('p') as $objPs){
                        $strFinalUrld = $objPs->getElementsByTagname('a')->item(0)->getAttribute('href');
                        $strFetchContent = $scraper->curlTheUrl($strFinalUrld);
                        if(stripos($strFetchContent, 'Company Name:') !== FALSE){
                             $objDomDoc = new DOMDocument();
                             @$objDomDoc->loadHTML($strFetchContent);
                             foreach($objDomDoc->getElementsByTagname('div') as $objDivs){
                                 if(trim($objDivs->getAttribute('class')) == 'searchResultDetails'){
                                     foreach($objDivs->getElementsByTagname('dl') as $objDls){
                                         $strCompName = trim($objDls->getElementsByTagname('dd')->item(0)->nodeValue);
                                         $strFirstName = trim($objDls->getElementsByTagname('dd')->item(2)->nodeValue);
                                         $strEmailId = trim($objDls->getElementsByTagname('dd')->item(5)->nodeValue);
                                         $strCsvTestData = $strCompName.','.$strFirstName.','.$strEmailId;
                                         fwrite($strCsvFile, $strCsvTestData.PHP_EOL);
                                     }
                                 }
                             }
                        }
                    }
                }
                fclose($strCsvFile);                
            }
        }
    }
}
$strURL = '
<div class="alert alert-success">
  <strong>Success!</strong> Click to Download CSV file <a href="save/ScrapedCSVFile.csv"> CSV file</a>
</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Scraper</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  <h2>Scraper</h2>
<form class="form-inline" method="post">
  <label class="mr-sm-2" for="inlineFormCustomSelect">Scrape</label>
  <select name="scp" class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect">
    <option selected>Choose...</option>
    <option value="1">cimaglobal</option>
  </select>
<?php echo $strURL;  
if(trim($strURL) == '') {
?>
  <button name="submit" type="submit" class="btn btn-primary">Submit</button>
  <?php } else {
  
  echo '<a href="scraper.php"> Back</a>';
  }
  ?>
</form>
</div>

</body>
</html>
