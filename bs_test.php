<!DOCTYPE html>
<html lang="en">

<head>
  <!-- CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

  <!-- jQuery and JS bundle w/ Popper.js -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  dgrgea
  <style>
    table {
      width: 300px;
      height: 200px;
      text-align: center;
      font-size: 20px;
    }

    table tr td {
      border: solid 1px;
    }
  </style>
</head>

<body>
  <input type="checkbox" id="c1" name="c1" value="1" onclick="show_table()">
  <label for="c1">1</label>
  <input type="checkbox" id="c2" name="c2" value="2">
  <label for="c2">2</label>
  <input type="checkbox" id="c3" name="c3" value="3">
  <label for="c3">3</label>
  <table>
    <colgroup>
      <col span="2" style="background-color: green;">
      <col style="background-color: greenyellow;">
    </colgroup>
    <tr>
      <th id="t1">
        <div id="d1">
          <button id="b1">btn</button>
          <ul>
            <a href="#" id="item" style="display: none;">Hide Colume</a>
          </ul>
        </div>
      </th>
      <th id="t2">2</th>
      <th id="t3">3</th>
      <th>4</th>
      <th>5</th>
      <th>6</th>
      <th>7</th>
    </tr>
    <tr>
      <td id="td1" class="tdd1">a</td>
      <td>b</td>
      <td>c</td>
      <td>d</td>
      <td>e</td>
      <td>f</td>
      <td>g</td>
    </tr>
    <tr>
      <td class="tdd1">a2</td>
      <td>b2</td>
      <td>c2</td>
      <td>d2</td>
      <td>e2</td>
      <td>f2</td>
      <td>g2</td>
    </tr>
  </table>



</body>

</html>

<script>
  function show_table() {
    if ($("#c1").prop("checked")) {
      $("#t1").css("display", "block");
    } else {
      $("#t1").css("display", "none");
    }
  }
  $(function() {
    $("#b1").on("click", function() {
      $("#item").toggle();
    })
    $("#item").on("click", function() {
      $("#item").toggle();
    })
  })
</script>