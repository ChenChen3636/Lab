<ul class="pagination">
    <li class="page-item disabled" id="firstPage">
        <a class="page-link" href="###" value="1">
            |< </a>
    </li>
    <li class="page-item disabled" id="prevPage">
        <a class="page-link" href="###" value="1"><i class="fas fa-chevron-up"></i>
        < </a>
    </li>
    <li class="page-item active">
        <a class="page-link" href="###" value="1">1</a>
    </li>
    <li class="page-item disabled">
        <a class="page-link" href="###" value="2">2</a>
    </li>
    <li class="page-item disabled" id="midPage">
        <a class="page-link" href="###" value="3">3</a>
    </li>
    <li class="page-item disabled">
        <a class="page-link" href="###" value="4">4</a>
    </li>
    <li class="page-item disabled">
        <a class="page-link" href="###" value="5">5</a>
    </li>
    <li class="page-item disabled" id="nextPage">
        <a class="page-link" href="###" value="2"><i class="fas fa-chevron-down"></i>></a>
    </li>
    <li class="page-item disabled" id="lastPage">
        <a class="page-link" href="###" value="44">>|</a>
    </li>
</ul>

<script>
    function final_page(count) {
        $("#lastPage").children(".page-link").attr("value", count);
    }
    var Pagination = function() {
        return {
            init: function() {
                $(".page-item").removeClass("active");
                $("#prevPage").next().addClass("active");

                $("#prevPage").find(".page-link").attr("value", 0);
                $("#prevPage").next().find(".page-link").attr("value", 1).text(1);
                $("#midPage").prev().find(".page-link").attr("value", 2).text(2);
                $("#midPage").find(".page-link").attr("value", 3).text(3);
                $("#midPage").next().find(".page-link").attr("value", 4).text(4);
                $("#nextPage").prev().find(".page-link").attr("value", 5).text(5);
                $("#nextPage").find(".page-link").attr("value", 2);
            },

            /** ----------------------------------------------
             * when choose page display corresponding btn style.
             ** ---------------------------------------------- */
            run: function(obj, count, rowEnd) {
                var val = parseInt(obj.find(".page-link").attr("value"));

                $("#prevPage>.page-link").attr("value", (val - 1));
                $("#nextPage>.page-link").attr("value", (val + 1));

                if (val <= 3) {
                    $("#prevPage").next().find(".page-link").attr("value", 1).text(1);
                    $("#midPage").prev().find(".page-link").attr("value", 2).text(2);
                    $("#midPage").find(".page-link").attr("value", 3).text(3);
                    $("#midPage").next().find(".page-link").attr("value", 4).text(4);
                    $("#nextPage").prev().find(".page-link").attr("value", 5).text(5);
                } else {
                    $("#prevPage").next().find(".page-link").attr("value", (val - 2)).text((val - 2));
                    $("#midPage").prev().find(".page-link").attr("value", (val - 1)).text((val - 1));
                    $("#midPage").find(".page-link").attr("value", val).text(val);
                    $("#midPage").next().find(".page-link").attr("value", (val + 1)).text((val + 1));
                    $("#nextPage").prev().find(".page-link").attr("value", (val + 2)).text((val + 2));
                }

                $(".page-item").removeClass("active");
                obj.closest(".pagination")
                    .find(`.page-item:nth-child(n+2):nth-child(-n+6)>.page-link[value=${val}]`)
                    .parent()
                    .addClass("active");
                this.status(count, rowEnd);
            },

            /** ----------------------------------------------
             * set current pagination btn status.
             ** ---------------------------------------------- */
            status: function(count, rowEnd) {
                $.each($(".pagination>li>a"), function(index, element) {
                    var val = parseInt($(element).attr("value"));
                    var quotient = parseInt((count / rowEnd));
                    var remainder = ((count % rowEnd) > 0) ? 1 : 0;
                    var parent = $(element).parent();

                    ((val <= (quotient + remainder)) && (val > 0)) ? parent.removeClass("disabled"):
                        parent.addClass("disabled");
                });
            }
        }
    }

    var pagination = Pagination();

    $(function() {
        $(window).scroll(function() {
            var scroll_y = $(this).scrollTop();

            $.each($(".paginationBlock"), function(index, element) {
                var pageElement = $(element).offset().top;

                if ((pageElement - scroll_y) < 30) {
                    $(element).children(".pagination").css({
                        "position": "fixed",
                        "top": "20px"
                    });
                } else {
                    $(element).children(".pagination").css({
                        "position": "unset"
                    });
                }
            });
        });
    });
</script>