<?php
 
 
 
 if(isset($_POST['taxonomy'])){
    $taxonomies = $_POST['taxonomy_type'];
    // $date = new DateTime();
    // $ts = $date->format("Y-m-d-G-i-s");
     $filename = "taxonomy_type.csv";
     header( 'Content-Type: text/csv' );
     header( 'Content-Disposition: attachment;filename='.$filename);
     $fp = fopen('php://output', 'w');
     $hrow =  unserialize(base64_decode($taxonomies[0]));
     fputcsv($fp, array_keys($hrow));
     foreach($taxonomies as $record):
            fputcsv($fp,  unserialize(base64_decode($record)) );
     endforeach;
     fclose($fp);
    
 }elseif($_POST['posts']){
     $posts = $_POST['post_type'];
    // $date = new DateTime();
    // $ts = $date->format("Y-m-d-G-i-s");
     $filename = "post-type.csv";
     header( 'Content-Type: text/csv' );
     header( 'Content-Disposition: attachment;filename='.$filename);
     $fp = fopen('php://output', 'w');
     $hrow =  unserialize(base64_decode($posts[0]));
     fputcsv($fp, array_keys($hrow));
     foreach($posts as $record):
            fputcsv($fp,  unserialize(base64_decode($record)) );
     endforeach;
     fclose($fp);
 }
 
 
 
 
 // ob_end_clean();
 
 
?>