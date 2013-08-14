<?php
function unzip($src_file, $dest_dir=false, $create_zip_name_dir=true, $overwrite=true)
{
  if ($zip = zip_open($src_file))
  {
    if ($zip)
    {
      $splitter = ($create_zip_name_dir === true) ? "." : "/";
      if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";
     
      // Create the directories to the destination dir if they don't already exist
      mkdir($dest_dir,0777,true);

      // For every file in the zip-packet
      while ($zip_entry = zip_read($zip))
      {
        // Now we're going to create the directories in the destination directories
       
        // If the file is not in the root dir
        $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
        if ($pos_last_slash !== false)
        {
          // Create the directory where the zip-entry should be saved (with a "/" at the end)
          mkdir($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1),0777,true);
        }

        // Open the entry
        if (zip_entry_open($zip,$zip_entry,"r"))
        {
         
          // The name of the file to save on the disk
          $file_name = $dest_dir.zip_entry_name($zip_entry);
         
          // Check if the files should be overwritten or not
          if ($overwrite === true || $overwrite === false && !is_file($file_name))
          {
            // Get the content of the zip entry
            $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            file_put_contents($file_name, $fstream );
            // Set the rights
            chmod($file_name, 0777);
            echo "save: ".$file_name."<br />";
          }
         
          // Close the entry
          zip_entry_close($zip_entry);
        }      
      }
      // Close the zip-file
      zip_close($zip);
    }
  }
  else
  {
    return false;
  }
 
  return true;
}

?>