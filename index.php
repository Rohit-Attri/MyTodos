<?php
// code of session to identify the user
session_start();
if ($_SESSION['loggedin'] != true) {
  header("location: login.php");
  exit;
}


require '_dbconnect.php';
$insert = false;
$update = false;
$delete = false;
// delete code
if (isset($_GET['delete'])) {
  // echo $_GET['delete']; just to check
  $dlsno = $_GET['delete'];
  try {
    $qry = "DELETE FROM `tododata` WHERE `sno` = '$dlsno';";
    $result = mysqli_query($conn, $qry);
    if ($result) {
      $delete = true;
    }
  } catch (Exception $e) {
    echo "There is an error in delete " . $e->getMessage();
  }
}

//update and insert code from both forms
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['editsno'])) {
    $title = $_POST['edittitle'];
    $desc = $_POST['editdesc'];
    $sno = $_POST['editsno'];
    // echo "we got it ". $_POST['editsno']; just to check
    // echo "we got ".$title." ".$desc." ".$sno; just to check
    try {
      $qry = "UPDATE `tododata` SET `title` = '$title', `description` = '$desc' WHERE `sno` = '$sno';";
      $result = mysqli_query($conn, $qry);
      if ($result) {
        $update = true;
      }
    } catch (Exception $e) {
      echo "There is an error occured to update the data" . $e->getMessage();
    }
  } else {
    $title = $_POST['title'];
    $desc = $_POST['desc'];
    $username = $_SESSION['username'];
    try {
      $qry = "INSERT INTO `tododata` (`username`,`title`,`description`) VALUES ('$username','$title','$desc');";
      $result = mysqli_query($conn, $qry);
      $aff = mysqli_affected_rows($conn);
      if ($result) {
        $insert = true;
      }
    } catch (Exception $e) {
      echo "There is an error To insert the data" . $e->getMessage();
    }
  }
}

?>

<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


  <!-- datatable  -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />

  <title>My ToDos</title>
</head>

<body>
  <!--Edit Modal -->
  <div class="modal fade" id="editTask" tabindex="-1" aria-labelledby="editTaskLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editTaskLabel">Edit Your Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="editsno" id="editsno">
            <div class="mb-3">
              <label for="edittitle" class="form-label">Task Title</label>
              <input type="text" class="form-control" id="edittitle" name="edittitle" aria-describedby="emailHelp">
            </div>
            <div class="mb-3">
              <label for="editdesc" class="form-label">Description</label>
              <textarea class="form-control" id="editdesc" name="editdesc" rows="3"></textarea>
            </div>
            <div class="modal-footer d-block mr-auto">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Update Task</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!--Edit modal end -->
  <?php require "Partials/nav.php" ?>
  <div style="height : 52px">
    <?php
    if ($insert) {
      echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success!</strong>Your Task has been submitted successfully.
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
    }
    if ($update) {
      echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Updated!</strong>Your Task has been updated successfully.
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
    }
    if ($delete) {
      echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
    <strong>Dleted!</strong>Your Task has been deleted successfully.
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
    }
    ?>
  </div>
  <div class="container my-3">
    <h3>
      <?php
      if ($_SESSION['username'] === 'Admin') {
        echo "Welcome " . $_SESSION['username'] . " Manage All Tasks!";

      } else {
        echo "Welcome " . $_SESSION['username'] . " Manage Your Tasks!";

      }
      ?>
    </h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="mb-3">
        <label for="Ttitle" class="form-label">Task Title</label>
        <input type="text" class="form-control" id="Ttitle" name="title" aria-describedby="emailHelp">
      </div>
      <div class="mb-3">
        <label for="desc" class="form-label">Description</label>
        <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Add Task</button>
    </form>
  </div>
  <div class="container">
    <table class="table" id="myTable">
      <thead>
        <tr>
          <th scope="col">Srno.</th>
          <?php
          if ($_SESSION['username'] === 'Admin') {
            echo " <th scope='col'>User</th>";
          }
          ?>
          <th scope="col">Title</th>
          <th scope="col">Description</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- add data from database to table -->
        <?php
        $username = $_SESSION['username'];
        $qry = "SELECT * FROM `tododata` WHERE `username`= '$username';";
        $qry2 = "SELECT * FROM `tododata`;";
        if ($username === 'Admin') {
          $result = mysqli_query($conn, $qry2);
          $num = mysqli_num_rows($result);
          $sno = 1;
          while ($row = mysqli_fetch_assoc($result)) {
            echo "
            <tr>
              <th scope='row'>$sno</th>
              <td>$row[username]</td>
              <td>$row[title]</td>
              <td>$row[description]</td>
              <td><button class='edit btn btn-primary' id='$row[sno]'>Edit</button> <Button class='delete btn btn-primary' id = 'd$row[sno]'>Delete</Button></td>
            </tr>";
            $sno = $sno + 1;
          }
        } else {
          $result = mysqli_query($conn, $qry);
          $num = mysqli_num_rows($result);
          $sno = 1;
          while ($row = mysqli_fetch_assoc($result)) {
            echo "
            <tr>
              <th scope='row'>$sno</th>
              <td>$row[title]</td>
              <td>$row[description]</td>
              <td><button class='edit btn btn-primary' id='$row[sno]'>Edit</button> <Button class='delete btn btn-primary' id = 'd$row[sno]'>Delete</Button></td>
            </tr>";
            $sno = $sno + 1;
          }
        }
        ?>
      </tbody>
    </table>
  </div>






  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->




  <script src="https://code.jquery.com/jquery-3.7.0.min.js"
    integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous">
    </script>

  <!-- datatable  -->
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
  <script>
    $(document).ready(function () {
      $('#myTable').DataTable();
    });
  </script>
  <!-- this javaScript is to add modals and confirm box button clicks and send data from javaScript to php server -->
  <script>
    // this code for admin table issue during editing
    const tuser = location.search;
    const urlparam = new URLSearchParams(tuser);
    const param1 = urlparam.get('tuser');



    let btns = document.getElementsByClassName('edit');
    let dls = document.getElementsByClassName('delete');
    var myModal = new bootstrap.Modal(document.getElementById('editTask'));
    // console.log(dls.length); mostly all comments are just to check that code is working fine or not
    // console.log(btns.length);
    // console.log(btns[0]);  always try these small thingss to check whether you are taking right step or not
    for (let i = 0; i < btns.length; i++) {
      btns[i].addEventListener("click", (e) => {
        // console.log('edit',e.target.parentNode.parentNode);
        tr = e.target.parentNode.parentNode;
        if (param1 === 'Admin') {
          title = tr.getElementsByTagName('td')[1].innerText;
          desc = tr.getElementsByTagName('td')[2].innerText;
        } else {
          title = tr.getElementsByTagName('td')[0].innerText;
          desc = tr.getElementsByTagName('td')[1].innerText;
        }
        // console.log(title,desc);
        edittitle.value = title;
        editdesc.value = desc;
        editsno.value = e.target.id;
        // console.log("buttons id is ",editsno.value); just to check wheather it is working or not
        myModal.toggle();
      });
    }

    for (let i = 0; i < dls.length; i++) {
      dls[i].addEventListener('click', (e) => {
        dlsno = e.target.id.substr(1,);
        sure = confirm(`You really want to delete this task?`);
        if (sure) {
          // console.log('yes')
          window.location = `/MyTodos/index.php?delete=${dlsno}`;
        } else {
          console.log('no');
        }
      })
    }
  </script>
</body>

</html>