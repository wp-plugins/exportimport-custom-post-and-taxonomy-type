<?php
function import_custom_post_type(){
 
 
 
   $importer = new cpt_import();
   if(isset($_POST)){
          $post = $_POST;
            switch ($_POST['action']) {
             case 'upload':
                 $file=$_FILES['uploadedfile'];
                 $importer->upload($file,$_POST['import_type']);
                 break;
             case 'import':
                 $importer->import($post,$_POST['import_type']);
                 break;
             case 'delete':
                   $importer->delete($post,$_POST['import_type']);
                 break;
          
             default:
            //  $importer->lister();
         }
   }
  $importer->lister();  
 }?>
   
   
   
  
 <?php
 
 class cpt_import {
    // url to Dribbble api
     public $post_dir;
     public $errors = array();
     public $messages = array();
     
    public function __construct()
        {
            $upload_dir = wp_upload_dir();
            $this->post_dir =$upload_dir['basedir'].'/cpt_import/post_types' ;
            $this->taxonomy_dir =$upload_dir['basedir'].'/cpt_import/taxonomy_types' ;
        }
     
    public function import($files,$type)
        {
           
           if($type == 'posts'){
             $dir = $this->post_dir;
           }elseif($type == 'taxonomies' ){
             $dir = $this->taxonomy_dir;
           }
           
            if(empty($files['select'])){
                $this->errors[]="Please select a file";
            }else {
                 foreach($files['select'] as $file):
                    $file_handle = fopen($dir.'/'.$file,"r");
                    $header = true;
                    $data= array();
                          
               
                
                
                     if($this->validate_format($file,$type)){
                         
                            while (!feof($file_handle) ) {
                              
                                if($header){
                                  $head =  fgetcsv($file_handle, 1024);
                                  $header = false;
                                }
                             
                                $raw =  fgetcsv($file_handle, 1024);   //fgetcsv($file_handle, 1024);
                                 if(!empty($raw)){
                                     $data[] = array_combine ($head, $raw) ; 
                                  }
                                
                            }
                             fclose($file_handle);
                          
                          $this->messages[] = $file .  " File imported succesfully.";
                         }else{
                             $this->errors[]= $file . " is not supported.";
                          } 
                       
                        
                      if($type == 'posts'){
                         update_option( 'wck_cptc', $data ); 
                      }elseif($type == 'taxonomies' ){
                         update_option( 'wck_ctc', $data );
                      }
                    
                endforeach;
            }
        }
   
   
   
   
    public function delete($files,$type){
          if(empty($files['select'])){
                $this->errors[]="Please select a file to delete";
            }else{
         
         
            foreach($files['select']  as $file):
             if ($type=='posts' && file_exists($this->post_dir.'/'.$file)) {
                  unlink($this->post_dir.'/'.$file);
                  $this->messages[] = $file . " deleted succesfully.";
              }elseif($type=='taxonomies' && file_exists($this->taxonomy_dir.'/'.$file)){
                  unlink($this->taxonomy_dir.'/'.$file);
                  $this->messages[] = $file . " deleted succesfully.";
               
              }
            endforeach;
        }
    }
    
   public function lister(){
   ?>     
       <div class='wrap'>
       <h2>Import custom post types</h2>
      <div class='messages'>
        
         <?php if($this->messages): ?>
             
             <?php foreach($this->messages as $message): ?>
                 <p class='green'><?php echo $message ?></p>
            <?php endforeach; ?>
             
         <?php endif; ?>
         
         
         <?php if($this->errors): ?>
            <?php foreach($this->errors as $error): ?>
                 <p class='red'><?php echo $error ?></p>
            <?php endforeach; ?>
         <?php endif; ?>
         
         
         
      </div>
       <div class='upload-form'>
       <p>Please upload a csv file here</p>  
       <form enctype="multipart/form-data" name="post-upload-form" action="" method="POST">
       <input type="hidden" name="action" value="upload" />
       <input type="hidden" name="import_type" value="posts" />
       <input name="uploadedfile" type="file" />
         <input type='submit' name ="post_import" value='Upload'/>
       </form>
       </div>
      
       <div class='files'>
       <form enctype="multipart/form-data" name="bulk-action-form" action="" method="POST">
       <?php if ( isset( $nonce ) ) echo $nonce ?>
       <div class="bulk-action-selector">
       <select name="action">
           <option value="" selected="selected">Select a action</option>
           <option value="delete">Delete</option>
           <option value="import">Import</option>
       </select>
       <input type="hidden" name="import_type" value="posts" />
       <input type='submit' value='Apply'/>
       </div>
        <div class='file-list'>
       <?php  
          if (file_exists( $this->post_dir )) {
              if ($handle = opendir( $this->post_dir )) {
   
               echo '<table><tr class="head"><td>Select</td><td>File Name</td><td>File Size</td><td>Date Uploaded</td></tr>';
                       while (false !== ($entry = readdir($handle))) {
                            
                            if ($entry != "." && $entry != "..") {
                                   $size = filesize($this->post_dir ."/" .$entry); 
                                   $date = filemtime($this->post_dir ."/" .$entry);
                                   echo '<tr>';
                                   echo '<td class="file-name"><input name="select[]" type="checkbox" value="'.$entry.'"></td>';
                                   echo '<td class="file-name">'.$entry.'</td>'; 
                                   echo '<td class="file-size">'.$this->FileSizeConvert($size).'</td>';
                                   echo '<td class="file-upload-date">'.date("F,j Y",$date).'</td>';
                                   echo '</tr>';
                                }
                           }
                echo '</table>';
               closedir($handle);
               }
   
          }else{
              mkdir(  $this->post_dir  , 0777, true);
               echo '<table width="100%" border="1"><tr><td colspan="3">No file exists!</td></tr></table>';
          }
        ?>
        </div>
       </div>
       </form>
       
   
   </div>
       
       
   
   
      <?php // taxonomy import form ?> 
       
       
       
     <div class='wrap'>
       <h2>Import custom taxonomies</h2>
      
       <div class='upload-form'>
            <p>Please upload a csv file here</p>  
       <form enctype="multipart/form-data" name="upload-form-taxonomy" action="" method="POST">
       <input type="hidden" name="action" value="upload" />
        <input type="hidden" name="import_type" value="taxonomies" />
       <input name="uploadedfile" type="file" />
         <input type='submit' name ="taxonomy_import" value='Upload'/>
       </form>
       </div>
      
       <div class='files'>
       <form enctype="multipart/form-data" name="bulk-taxonomy-action-form" action="" method="POST">
       <?php if ( isset( $nonce ) ) echo $nonce ?>
       <div class="bulk-action-selector">
       <select name="action">
           <option value="" selected="selected">Select a action</option>
           <option value="delete">Delete</option>
           <option value="import">Import</option>
       </select>
       <input type="hidden" name="import_type" value="taxonomies" />
       <input type='submit' value='Apply'/>
       </div>
        <div class='file-list'>
       <?php  
          if (file_exists( $this->taxonomy_dir )) {
              if ($handle = opendir( $this->taxonomy_dir )) {
   
               echo '<table><tr class="head"><td>Select</td><td>File Name</td><td>File Size</td><td>Date Uploaded</td></tr>';
                       while (false !== ($entry = readdir($handle))) {
                            
                            if ($entry != "." && $entry != "..") {
                                   $size = filesize($this->taxonomy_dir ."/" .$entry); 
                                   $date = filemtime($this->taxonomy_dir ."/" .$entry);
                                   echo '<tr>';
                                   echo '<td class="file-name"><input name="select[]" type="checkbox" value="'.$entry.'"></td>';
                                   echo '<td class="file-name">'.$entry.'</td>'; 
                                   echo '<td class="file-size">'.$this->FileSizeConvert($size).'</td>';
                                   echo '<td class="file-upload-date">'.date("F,j Y",$date).'</td>';
                                   echo '</tr>';
                                }
                           }
                echo '</table>';
               closedir($handle);
               }
   
          }else{
              mkdir(  $this->taxonomy_dir  , 0777, true);
               echo '<table width="100%" border="1"><tr><td colspan="3">No file exists!</td></tr></table>';
          }
        ?>
        </div>
       </div>
       </form>
       
   
   </div>      
    
   
   <?php }  
    /**
   * Converts bytes into human readable file size.
   *
   * @param string $bytes
   * @return string human readable file size (2,87 ??)
   * @author Mogilev Arseny
   */
    function FileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

    foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
       return $result;
    }
    
    
    
    public function upload($files,$type){
   
     // print_r($files);
        if($files['size']<=0){
                $this->errors[]="Please select a file to upload.";
                
        }elseif($files['type']!='text/csv'){
             $this->errors[]="File type not supported.Please upload csv file only.";
        }else{
         
                  if($type =='taxonomies'){
                     move_uploaded_file( $_FILES['uploadedfile']['tmp_name'], $this->taxonomy_dir . '/' . $files['name']  );
                  }elseif($type =='posts'){
                     move_uploaded_file( $_FILES['uploadedfile']['tmp_name'], $this->post_dir . '/' . $files['name']  );
                  }
       
          $this->messages[] = "File uploaded succesfully.";
        }
    }
    
    public function validate_format($file,$type){
    
    
       $handle = fopen($this->taxonomy_dir.'/'.$file,"r");
      $head =  fgetcsv($handle, 1024);
      
      
       if($type =='taxonomies'){
            $valid_header = array( 'taxonomy','singular-label','plural-label','attach-to', 'hierarchical','search-items','popular-items','all-items','parent-item','parent-item-colon','edit-item','update-item','add-new-item','new-item-name', 'separate-items-with-commas', 'add-or-remove-items', 'choose-from-most-used', 'menu-name', 'public','show-ui', 'show-tagcloud','show-admin-column',);
            $diff = array_diff($head,$valid_header);
             
               if(count($diff)==0){
                  return true;
               }else{
                  return false;
               }
                
 
          }elseif($type =='posts'){
            $valid_header = array( 'post-type', 'description','singular-label', 'plural-label','hierarchical', 'has-archive','supports','add-new', 'add-new-item','edit-item','new-item', 'all-items','view-items', 'search-items','not-found', 'not-found-in-trash', 'parent-item-colon', 'menu-name', 'public', 'show-ui','show-in-nav-menus','show-in-menu','menu-position', 'menu-icon', 'capability-type','taxonomies', 'rewrite','rewrite-slug',);
            $diff = array_diff($head,$valid_header);
             
               if(count($diff)==0){
                  return true;
               }else{
                  return false;
               }
                     
       }
      
    }
    
}
?>