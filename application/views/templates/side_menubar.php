<!DOCTYPE html>
<html>
<head>
  <title></title>
  <link rel="stylesheet" href="css/main.css">
  <link  rel="stylesheet" href="stock-v2/assets/bower_components/bootstrap/dist/css/bootstrap.css"/>
 <link  rel="stylesheet" href="stock-v2/assets/bower_components/bootstrap/dist/css/bootstrap-theme.min.css"/>  
  <script src="stock-v2/assets/bower_components/bootstrap/dist/js/jquery.js" type="text/javascript"></script>
 <!-- <script src="js/validation.js" type="text/javascript"></script>
-->
  <script src="stock-v2/assets/bower_components/bootstrap/dist/js/bootstrap.min.js"  type="text/javascript"></script> 
</head>
<body>
<nav class=" navbar-inverse" style="min-height: 60px">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span> 
      </button>
      <a href="<?php echo base_url('Dashboard/') ?>">
    <?php  echo'<img src="'.base_url('logo.jpg').'" width="100px" height="50px" style="margin-top: 3px; border-radius: 5px"></img>'; ?></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
       
        <li><a href="<?php echo base_url('Orders/') ?>">
                <i class="fa fa-dollar"></i> <span>POIC</span>
              </a></li>
    
        <li><a href="<?php echo base_url('Products/') ?>">
                <i class="fa fa-cube"></i>
                <span>Products</span>
                <span class="pull-right-container">
                
                </span>
              </a></li>
                    <li> <?php if(in_array('createService', $user_permission) || in_array('updateService', $user_permission) || in_array('viewService', $user_permission) || in_array('viewService', $user_permission)): ?>
            <li>
              <a href="<?php echo base_url('Services/') ?>">
                <i class="fa fa-gear"></i> <span>Services</span>
              </a>
            </li>
          <?php endif; ?></li>

        <li> <?php if(in_array('createBrand', $user_permission) || in_array('updateBrand', $user_permission) || in_array('viewBrand', $user_permission) || in_array('deleteBrand', $user_permission)): ?>
            <li id="brandNav">
              <a href="<?php echo base_url('brands/') ?>">
                <i class="glyphicon glyphicon-tags"></i> <span>Brands</span>
              </a>
            </li>
          <?php endif; ?></li>

      
      
</li> 
        <li><?php if(in_array('createCategory', $user_permission) || in_array('updateCategory', $user_permission) || in_array('viewCategory', $user_permission) || in_array('deleteCategory', $user_permission)): ?>
            <li id="categoryNav">
              <a href="<?php echo base_url('category/') ?>">
                <i class="fa fa-files-o"></i> <span>Category</span>
              </a>
            </li>
          <?php endif; ?></li> 
          <li> <?php if(in_array('updateCompany', $user_permission)): ?>
            <li id="companyNav"><a href="<?php echo base_url('company/') ?>"><i class="fa fa-files-o"></i> <span>Company</span></a></li>
          <?php endif; ?></li>
            <li><?php if(in_array('viewReports', $user_permission)): ?>
            <li id="reportNav">
              <a href="<?php echo base_url('reports/') ?>">
                <i class="glyphicon glyphicon-stats"></i> <span>Reports</span>
              </a>
            </li>
          <?php endif; ?>

          

      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="<?php echo base_url('auth/logout') ?>"><i class="glyphicon glyphicon-log-out"></i> <span>Logout</span></a></li>
      </ul>
    </div>
  </div>
</nav>
</body>
</html>
