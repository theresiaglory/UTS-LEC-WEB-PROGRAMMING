<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventName = $_POST['event_name'];
    $eventDate = $_POST['event_date'];
    $eventTime = $_POST['event_time'];
    $eventLocation = $_POST['event_location'];
    $eventDescription = $_POST['event_description'];
    $maxParticipants = $_POST['max_participants'];
    
    $image = $_FILES['event_image'];
    $imageName = $image['name'];
    $imageTmpName = $image['tmp_name'];
    $imageFolder = 'uploads/' . $imageName;
    if (move_uploaded_file($imageTmpName, $imageFolder)) {
        $sql = "
            INSERT INTO events (eventName, date, time, location, description, max_participants, image) 
            VALUES ('$eventName', '$eventDate', '$eventTime', '$eventLocation', '$eventDescription', $maxParticipants, '$imageName')
        ";
        if ($conn->query($sql) === TRUE) {
            echo "<script type='text/javascript'>
                alert('Event created successfully!');
                window.location.href = 'dashboard.php';
              </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error uploading image!";
    }
}
?>