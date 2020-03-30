<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Venn diagram of Venn diagrams</title>
    <link href="style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="d3.js"></script>
<style>
body {
font-size : 16px;
font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
}
</style>
</head>

<body>
    <div id="inputs">
        <p>
            <button id="largeGroups" onclick="drawButton()">ReDraw</button>
        </p>
        <p>
            <label for="largeGroups">Group's Filter</label> <br>
                    
            <input type="checkbox" value="blueapron" checked>
            <label for="blueapron"> blueapron.com</label><br>
            <input type="checkbox" value="homechef" checked>
            <label for="homechef"> homechef.com</label><br>
            <input type="checkbox" value="hellofresh" checked>
            <label for="hellofresh"> hellofresh.com</label><br>
            <input type="checkbox" value="plated">
            <label for="plated"> plated.com</label><br>
            <input type="checkbox" value="greenchef">
            <label for="greenchef"> greenchef.com</label><br>
            <input type="checkbox" value="verywellfit">
            <label for="verywellfit"> verywellfit.com</label><br>
        </p>
        <p>
            <label for="smalldots">Ranking Position</label>
            <input type="number" min="2" step="1" max="600" value="11" />
        </p>
    </div>
    <div id="venn"></div>
</body>

<script src="d3.v4.min.js"></script>
<script src="venn.js"></script>
<script>

const fileName = [
    'sample.csv'
]

var importData;

var loadFileAfterRender = (fileNames) => {
    return new Promise(
        async (resolve) => {
            d3
                .csv(fileNames[0])
                .row(function(d) { 
                    return {
                        keyword:d.Keyword, blueapron: Number(d.blueapron.trim()), homechef: Number(d.homechef.trim()), hellofresh: Number(d.hellofresh.trim())
                        , plated: Number(d.plated.trim()), greenchef: Number(d.greenchef.trim()), verywellfit: Number(d.verywellfit.trim())
                    }
                })
                .get(function(error, data){
                    resolve(data)
                });
        }
    )
};

loadFileAfterRender(fileName)
.then(
    function (data) {
        // load Completed
        importData = data;
        
        drawDiagram(importData)
    }
)

function drawDiagram(data, size) {
    var panel = d3.select("#venn").remove();

    d3.select('body')
        .append('div')
        .attr("id", "venn")

    var setsArr = [],
        setsNodeArr = [],
        num0 = 0,
        num1 = 0,
        num2 = 0,
        num3 = 0,
        num4 = 0,
        num5 = 0,
        num6 = 0,
        keyword0 = [],
        keyword1 = [],
        keyword2 = [],
        keyword3 = [],
        keyword4 = [],
        keyword5 = [],
        keyword6 = [];

    d3
        .selectAll('#inputs input')
        .each(function() {
            var sel = d3.select(this)
            if (this.type == 'checkbox' && this.checked) { //For checking LargeGroup
                setsArr.push(this.value);
            }
            if (this.type == 'number') { //For checking ranking position
                for (var i = 0; i < data.length; ++i) {
                    var includingValue = 0,
                    setsNodeTemp = []

                    for (var key in data[i]) {
                        if (includingValue >=3) break
                        for (var k = 0; k < setsArr.length; k++) {
                            if (key != setsArr[k]) continue
                            
                            if ( this.value > data[i][setsArr[k]] && data[i][setsArr[k]] != 0) {
                                includingValue++
                                break
                            } else {
                                setsNodeTemp.push(k)
                            }
                        }
                    }
                    if (includingValue == 3) { num6++; keyword6.push(data[i].keyword);}
                    else if (includingValue == 2) {
                        if (setsNodeTemp[0] == 0) { num5++; keyword5.push(data[i].keyword);}
                        if (setsNodeTemp[0] == 1) { num4++; keyword4.push(data[i].keyword);}
                        if (setsNodeTemp[0] == 2) { num3++; keyword3.push(data[i].keyword);}
                    } else if (includingValue == 1) {
                        if (setsNodeTemp[0] == 0 && setsNodeTemp[1] == 1) { num2++; keyword2.push(data[i].keyword);} 
                        if (setsNodeTemp[0] == 0 && setsNodeTemp[1] == 2) { num1++; keyword1.push(data[i].keyword);} 
                        if (setsNodeTemp[0] == 1 && setsNodeTemp[1] == 2) { num0++; keyword0.push(data[i].keyword);} 
                    }
                }
                
            }
        })
        console.log('dots:  ' + setsArr[0] + ':' + num0 + '    ' + setsArr[1] + ':' + num1 + '    ' + setsArr[2] + ':' + num2 + '    '
        + setsArr[0] + '-' + setsArr[1] + ':' + num3 + '    ' + setsArr[0] + '-' + setsArr[2] + ':' + num4 + '    '
        + setsArr[1] + '-' + setsArr[2] + ':' + num5 + '    ' + setsArr[0] + '-' + setsArr[1] + '-' + setsArr[2] + ':' + num6)
    var sets = [
            {sets: [setsArr[0]], size: 12, dots: num0, tips: keyword0},
            {sets: [setsArr[1]], size: 12, dots: num1, tips: keyword1},
            {sets: [setsArr[2]], size: 12, dots: num2, tips: keyword2},
            {sets: [setsArr[0], setsArr[1]], size: 4, dots: num3, tips: keyword3},
            {sets: [setsArr[0], setsArr[2]], size: 4, dots: num4, tips: keyword4},
            {sets: [setsArr[1], setsArr[2]], size: 4, dots: num5, tips: keyword5},
            {sets: [setsArr[0], setsArr[1], setsArr[2]], size: 2, dots: num6, tips: keyword6}
    ];

    var chart = venn.VennDiagram()
        chart.wrap(false)
        .width(1000)
        .height(900);

    var div = d3.select("#venn").datum(sets).call(chart);
    // div.selectAll("text").style("fill", "white");
    div.selectAll("text").style("fill", "blue");
    div.selectAll(".venn-circle path").style("fill-opacity", .6);
}

function drawButton() {
    var filters = 0;

    d3.selectAll('#inputs input')
    .each(function() {
        if (this.checked) filters++;
    })

    if (filters != 3) alert("You can work only 3 Groups.")
    else drawDiagram(importData, 15);
}
</script>
</html>
