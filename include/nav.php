<nav class="navbar navbar-expand-md bg-primary navbar-dark fixed-top">
            <a class="navbar-brand" href="#"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <strong><a class="nav-link <?php echo $pageName=='Home'? 'active':''; ?>" href="index.php">Home</a></strong>
                    </li>
                    <li class="nav-item">
                        <strong><a class="nav-link <?php echo $pageName=='All research'? 'active':''; ?>" href="allResearch.php">All research</a></strong>
                    </li>
                    <li class="nav-item">
                        <strong><a class="nav-link <?php echo $pageName=='Professors'? 'active':''; ?>" href="professors.php">Professors</a></strong>
                    </li>
                    <li class="nav-item">
                        <strong><a class="nav-link <?php echo $pageName=='All years'? 'active':''; ?>" href="allYears.php">All years</a></strong>
                    </li>
                    <li class="nav-item">
                        <strong><a class="nav-link <?php echo $pageName=='Timeline'? 'active':''; ?>" href="timeline.php">Timeline</a></strong>
                    </li>
                    <li class="nav-item">
                        <strong><a class="nav-link <?php echo $pageName=='Journals'? 'active':''; ?>" href="journals.php">Journals</a></strong>
                    </li>
                    <li class="nav-item">
                        <strong><a class="nav-link <?php echo $pageName=='Conferences'? 'active':''; ?>" href="conferences.php">Conferences</a></strong>
                    </li>
                </ul>
            </div>    
        </nav>

