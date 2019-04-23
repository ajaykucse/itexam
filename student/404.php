  <div class="row">
      <div class="col-md-12">
          <div class="error-template">
              <h1>
                  Oops!</h1>
              <h2>
                  404 Not Found</h2>
              <div class="error-details">
                  Sorry, an error has occured, Requested page not found!
              </div>
              <div class="error-actions">
<?php		if (isset($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']))
{
?>
                  <a href="<?php echo $URL;?>student.html" class="btn btn-primary btn-lg"><span class="fa fa-cogs"></span>
                      Back </a>
<?php
}
else
{
	?>
                  <a href="<?php echo $MAIN_URL;?>" class="btn btn-primary btn-lg"><span class="fa fa-home"></span>
                      Take Me Home </a>
<?php
}
?>
              </div>
          </div>
      </div>
  </div>
<style>
.error-template {
    padding: 80px 15px;
    text-align: center;
}
.error-template h1 {
    display: block;
    font-size: 3em;
    font-weight: bold;
}
.error-template h2 {
    display: block;
}

.error-actions {
    margin-top: 15px;
    margin-bottom: 15px;
}
</style>