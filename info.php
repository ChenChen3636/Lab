<?php
// var_dump($_POST);
?>
<html>
    <head>
        <title></title>
        <!-- CSS only -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link rel="stylesheet" href="shop/css/TEST.css">
        <!-- JS, Popper.js, and jQuery -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        <style>
            .inputbox{
                border-radius: 10px;
            }
            .tableblock{
                padding:100px;
                color:white;
            }
            font{
                color:white;
            }
            .logout{
                float:right;
                padding:10px;
            }

                /* Gradient Love */
                .header {
                    background: linear-gradient(-38deg, #92f3de,#926dd8 );
                    background-size: 200% 200%;
                    animation: verticalBreathing 15s ease infinite;
                }

                @keyframes verticalBreathing {
                    0%   { background-position: 0% 0%;  }
                    25%  { background-position: 40% 40%; }
                    50%  { background-position: 80% 80%; }
                    75%  { background-position: 20% 60%; }
                    100% { background-position: 0% 0%;  }
                }


                /* ====================== */
                /* Typography & layout stuff */

                /* .header {
                    display: flex;
                    height: 100vh;
                    justify-content: center;
                    align-items: center;
                } */

                .content {
                    max-width: 600px;
                    width: 90%;
                }

                /* Boring Stuff */
                html, body {
                    height: 100%;
                    width: 100%;
                    margin: 0;
                    padding: 0;
                    text-rendering: optimizeLegibility;
                    -webkit-font-smoothing: antialiased;
                }

                @import url('https://fonts.googleapis.com/css?family=Montserrat:900');

                h1 {
                margin: 0;
                font-family: 'Montserrat', sans-serif;
                font-size: 10vh;
                letter-spacing: 1vw;
                position: absolute;
                top: 5px;
                left: 50%;
                transform: translateX(-50%);
                color: white;
                // background:url('https://i.pinimg.com/originals/9f/9c/c5/9f9cc5217bb04790e5fb852b665ca2ff.jpg');
                background:url('https://thumbs.dreamstime.com/b/tropical-flower-pattern-seamless-flowers-blossom-flowers-nature-background-vector-illustration-61142748.jpg');
                background-size: auto 200%;
                background-clip: text;
                text-fill-color: transparent;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: shine 8s linear infinite;
                &:nth-child(2) {
                    top: 50vh;
                }
                @keyframes shine {
                    from {
                    background-position: center 0;
                    }
                    to {
                    background-position: center 200%;
                    }
                }
                }


                
                .addBtn{
                    position: relative;
                }
                #createBtn,#deleteBtn,#editBtn{
                    position: absolute;
                    z-index: -1;
                   
                }
                #deleteBtn+label{
                    top:120px;
                }
                #editBtn+label{
                    top:60px;
                }
    
                input[type="submit"]+label{
                display: block;
                width: 104px;
                height: 40px;
                z-index: 1;
                background: #fff;
                }
                input[type="submit"]+label:after {
                content: '';
                background: linear-gradient(120deg, #6559ae, #ff7159, #6559ae);
                background-size: 400% 400%;
                -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 100px 4px, 100px 36px, 4px 36px, 4px 100%, 100% 100%, 100% 0%, 0% 0%);
                -moz-animation: gradient 3s ease-in-out infinite, border 1s forwards ease-in-out reverse;
                -webkit-animation: gradient 3s ease-in-out infinite, border 1s forwards ease-in-out reverse;
                animation: gradient 3s ease-in-out infinite, border 1s forwards ease-in-out reverse;
                }
                input[type="submit"]+label > span {
                display: block;
                background: linear-gradient(120deg, #6559ae, #ff7159, #6559ae);
                background-size: 400% 400%;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                -moz-animation: gradient 3s ease-in-out infinite;
                -webkit-animation: gradient 3s ease-in-out infinite;
                animation: gradient 3s ease-in-out infinite;
                }

                helpers
                .absolute-centering, body:after, input[type="submit"]+label, input[type="submit"]+label:after {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                }

                .text-formatting, input[type="submit"]+label {
                text-transform: uppercase;
                text-decoration: none;
                text-align: center;
                letter-spacing: 2px;
                line-height: 42px;
                font-family: 'Squada One', cursive;
                font-size: 20x;
                }

                /* motion */
                @-moz-keyframes gradient {
                0% {
                    background-position: 14% 0%;
                }
                50% {
                    background-position: 87% 100%;
                }
                100% {
                    background-position: 14% 0%;
                }
                }
                @-webkit-keyframes gradient {
                0% {
                    background-position: 14% 0%;
                }
                50% {
                    background-position: 87% 100%;
                }
                100% {
                    background-position: 14% 0%;
                }
                }
                @keyframes gradient {
                0% {
                    background-position: 14% 0%;
                }
                50% {
                    background-position: 87% 100%;
                }
                100% {
                    background-position: 14% 0%;
                }
                }
                @-moz-keyframes border {
                0% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 100px 4px, 100px 36px, 4px 36px, 4px 100%, 100% 100%, 100% 0%, 0% 0%);
                }
                25% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 100px 4px, 100px 36px, 100px 36px, 100px 100%, 100% 100%, 100% 0%, 0% 0%);
                }
                50% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 100px 4px, 100px 4px, 100px 4px, 100px 4px, 100px 4px, 100% 0%, 0% 0%);
                }
                75% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 4px 4px, 4px 4px, 4px 4px, 4px 4px, 4px 4px, 4px 0%, 0% 0%);
                }
                100% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 0% 100%);
                }
                }
                @-webkit-keyframes border {
                0% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 100px 4px, 100px 36px, 4px 36px, 4px 100%, 100% 100%, 100% 0%, 0% 0%);
                }
                25% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 100px 4px, 100px 36px, 100px 36px, 100px 100%, 100% 100%, 100% 0%, 0% 0%);
                }
                50% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 100px 4px, 100px 4px, 100px 4px, 100px 4px, 100px 4px, 100% 0%, 0% 0%);
                }
                75% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 4px 4px, 4px 4px, 4px 4px, 4px 4px, 4px 4px, 4px 0%, 0% 0%);
                }
                100% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 0% 100%);
                }
                }
                @keyframes border {
                0% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 100px 4px, 100px 36px, 4px 36px, 4px 100%, 100% 100%, 100% 0%, 0% 0%);
                }
                25% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 100px 4px, 100px 36px, 100px 36px, 100px 100%, 100% 100%, 100% 0%, 0% 0%);
                }
                50% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 100px 4px, 100px 4px, 100px 4px, 100px 4px, 100px 4px, 100% 0%, 0% 0%);
                }
                75% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 4px, 4px 4px, 4px 4px, 4px 4px, 4px 4px, 4px 4px, 4px 0%, 0% 0%);
                }
                100% {
                    -webkit-clip-path: polygon(0% 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 4px 100%, 0% 100%);
                }
                }

                                
        </style>
        
    </head>
    <body>
        <div class="header">
            <div><button onclick="location.href='index.html'" class="learn-more">logout</button></div>
            <div><h1>Product List</h1></div>
            <div>
                <form action="info.php?id=$id" method="POST">
                    <div class="tableblock">
                        <table class="table table-bordered" name="mytable">
                        <thead>
                            <tr>
                                <th></th>
                                <th scope="col"><font>ID</font></th>
                                <th scope="col"><font>水果</font></th>
                                <th scope="col"><font>價格</font></th>
                                <th scope="col"><font>操作</font></th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            <tr>
                                <th></th>
                                <th></th>
                                <td><input type="text" class="inputbox" placeholder="banana" name="fruit"></td>
                                <td><input type="text" class="inputbox" placeholder="35" name="price"></td>
                                <td class="addBtn">
                                    <input id="createBtn" type="submit" value="新增" name="btn">
                                    <label for="createBtn"><span>Create</span></label>
                                </td>
                            </tr>
                            
                            <?php
                                $db = new PDO('mysql:host=localhost;dbname=shop;charset=utf8', 'pipi', '123456');
                                //連接測試
                                if($db){
                                    
                                }else{
                                    echo "error";
                                }

                                
                                $sql = "CREATE TABLE product (
                                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                fruit VARCHAR(10) NOT NULL UNIQUE,
                                price INT(6) NOT NULL)";
                                $db->query($sql);

                                if((isset($_POST["fruit"])) && (isset($_POST["price"]))){
                                    $newFruit = $_POST["fruit"];
                                    $newPrice = $_POST["price"];
                                }
                            
                                //新增data  
                                if(isset($_POST['btn'])){
            
                                    if(empty($newFruit)){
                                    echo "//fruit is empty";
                                    }
                                    else if(empty($newPrice)){
                                    echo "//price is empty";
                                    }
                                    else{
                                        $rs = $db->exec("INSERT INTO product (fruit,price)values('$newFruit','$newPrice')");
                                    }
                                    

                                }
                                
                                //最後一個新增的id
                                // $nRows = $db->query('select count(*) from product')->fetchColumn(); 

                                // echo "row: ".$nRows."***" ;
                                
                                //修改
                                if(isset($_POST['modify'])){
                                    $id = $_POST['select'];
                                    $price = $_POST['newprice'];
        
                                    $sql = "UPDATE product SET price = :price WHERE id = :id";
                                    $sth = $db->prepare($sql);
                                    $sth->execute(array("price" => "$price","id"=>"$id"));
                                }
                                
                                //刪除
                                if(isset($_POST['delete'])){
                                    
                                    if(isset($_POST['select'])){
                                         $id = $_POST['select'];
                                        $sql = "DELETE FROM product Where id=:id";
                                        $sth = $db->prepare($sql);
                                        $sth->execute(array("id"=>"$id"));
                                    }
                                }

                                //取得整個table
                                $statement = $db->query('select * from product');
                                $getData = $statement->fetchAll(PDO::FETCH_ASSOC);

                                //印出table
                                foreach($getData as $index=>$row){
                                    $id=$row['id'];
                                    $price=$row['price'];
                                    $str= "<tr>
                                    <td><input type='radio' name='select' value='$id'></td>
                                    <td><font>".($index+1)."</font></td>
                                    <td><font>".$row['fruit']."</font></td>
                                    <td><font>".$row['price']."</font></td>";
                                    
                                    if($index==0){
                                        $str .= "<td class='addBtn 'rowspan='".count($getData)."'>
                                                    <input type='submit' value='修改' name='modify' id='editBtn'>
                                                    <label for='editBtn'><span>Edit</span></label>
                                                    <input type='text' class='inputbox' name='newprice' size='4' placeholder='price  '><br><br>
                                                    <input type='submit' value='刪除' name='delete' id='deleteBtn'>
                                                    <label for='deleteBtn'><span>Delete</span></label>
                                                </td>
                                                
                                        </tr>";
                                    }else{
                                        $str .= "</tr>";
                                    }
                                    
                                    echo $str;
                                    
                                }
                                

                            ?>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
