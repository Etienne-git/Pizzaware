<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     PageTemplate.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.1
 */

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

/**
 * This is a template for top level classes, which represent
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class.
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking
 * during implementation.
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 */
class Driver extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks

    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }

    /**
     * Cleans up whatever is needed.
     * Calls the destructor of the parent i.e. page class.
     * So, the database connection is closed.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
	 * @return array An array containing the requested data. 
	 * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData():array
    {
        // to do: fetch data for this view from the database
		// to do: return array containing data
        $data = array();
        $sql = "SELECT sub.ordering_id, sub.address, sub.status, count(sub.ordering_id) - sum(sub.status / 2) AS finished 
                FROM (SELECT o.ordering_id, o.address, oa.status 
                      FROM ordering o INNER JOIN ordered_article oa ON o.ordering_id = oa.ordering_id) sub 
                GROUP BY sub.ordering_id HAVING finished <= 0; ";
        $recordset = $this->_database->query($sql);
        if (!$recordset) {
            throw new Exception("Fehler in Abfrage: " . $this->_database->error);
        }
        while ($record = $recordset->fetch_assoc()) {
            $data[] = $record;
        }
        $recordset->free();
        return $data;
    }

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
	 * @return void
     */
    protected function generateView():void
    {
		$data = $this->getViewData();
        $this->generatePageHeader('Fahrer'); //to do: set optional parameters
        // to do: output view of this page
        echo <<<EOT
        <body>
            <header>
                <nav></nav>
            </header>
            <div id="navbar"></div>
            <main>
                <script>
                $(function(){
                $("#navbar").load("navbar.html");
                });
                 </script>
                <section>
                    <h2>Auslieferbare Bestellungen</h2>
        EOT;
        $this->displayMainInformation($data);

        $this->generatePageFooter();
    }


    protected function displayMainInformation(array $dataRows): void{
        foreach($dataRows as $row){
            $ordering_id = $row["ordering_id"];
            $address = $row["address"];

            echo <<<EOT
            <article>
                 <h3 > Bestellung: $ordering_id </h3 >
                 <form action = "DriverPage.php" method = "post" id = form_{$ordering_id} accept-charset = "UTF-8" >
            EOT;
             echo   "<p>" . htmlspecialchars($address) . "</p>";

            $isChecked = ($row['status'] == 2) ? 'checked' : 'disabled';
            echo <<<EOT
            <input type = "radio" id = done_$ordering_id name = "check_order" value = "2" onclick="document.forms['form_{$ordering_id}'].submit();"  $isChecked />
            <label for = done_$ordering_id >Fertig</label ><br>
            EOT;

            $isChecked = ($row['status'] == 3) ? 'checked' : '';
            echo <<<EOT
            <input type = "radio" id = route_$ordering_id name = "check_order" value = "3" onclick="document.forms['form_{$ordering_id}'].submit();" $isChecked />
            <label for = route_$ordering_id >Unterwegs</label ><br>
            <input type = "radio" id = delivered_$ordering_id name = "check_order" value = "4" onclick="document.forms['form_{$ordering_id}'].submit();" />
            <label for = delivered_$ordering_id >Geliefert</label ><br>
            <input type="hidden" name="article_id" value=$ordering_id  />
            </form >
            </article >
            EOT;

        }
        echo <<<EOT
        </section>
        EOT;
    }

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
	 * @return void
     */
    protected function processReceivedData():void
    {
        parent::processReceivedData();
        // to do: call processReceivedData() for all members
        if(isset($_POST['article_id']) && isset($_POST['check_order'])) {
            $id = $_POST['article_id'];
            $newStatus = $_POST['check_order'];

            if($newStatus == 4){
                $SQL = "DELETE FROM ordered_article WHERE ordering_id =" . $id ;
                $this->_database->query($SQL);

                $SQL = "DELETE FROM ordering WHERE ordering_id =" . $id ;
                $this->_database->query($SQL);
            }else{
                $SQL = "UPDATE ordered_article SET status= $newStatus WHERE ordering_id=" . $id;
                $this->_database->query($SQL);
            }
            $location = "http://localhost/Praktikum/Prak5/BakerPage.php";
            if(Count($_POST)){
                header("HTTP/1.1 303 See Other");
                header("Location: ". $location);
                die(); //
            }
        }
    }

    /**
     * This main-function has the only purpose to create an instance
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
	 * @return void
     */
    public static function main():void
    {
        try {
            $page = new Driver();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
Driver::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >