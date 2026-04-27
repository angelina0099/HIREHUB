<?php if (!empty($row['image_path'])) { ?>
    <img src="uploads/jobs/<?php echo htmlspecialchars($row['image_path']); ?>"
         width="200" alt="Job Image">
        
<?php } ?>
