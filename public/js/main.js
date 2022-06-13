const $ID = function (id) {
    return document.getElementById(id);
};
const $QSA = document.querySelectorAll.bind(document);

function docReady(fn) {
    if (document.readyState === "complete" || document.readyState === "interactive") {
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

function changeTileType() {
    if (this.value === 'click') {
        $ID('layer_planner_gap_width').disabled = true;

        return false;
    }

    $ID('layer_planner_gap_width').disabled = false;
}

function addTileTypeSelectionBehavior() {
    let currentTileType = $ID('layer_planner_tile_type').value;
    $ID('layer_planner_gap_width').disabled = currentTileType !== 'tile';
    $ID('layer_planner_tile_type').addEventListener('change', changeTileType);
}

function addMouseOver() {
    let number = this.querySelector('span.number').innerText;
    let tilesWithSameNumber = $QSA('.number' + number);

    tilesWithSameNumber.forEach(tile => {
        tile.classList.add('hover')
    });
}

function addMouseOut() {
    let number = this.querySelector('span.number').innerText;
    let tilesWithSameNumber = $QSA('.number' + number);

    tilesWithSameNumber.forEach(tile => {
        tile.classList.remove('hover')
    })
}

function addPrintListener() {
    let printButton = $ID('print');

    if (printButton) {
        printButton.addEventListener('click', function () {
            printResult();
        })
    }
}

function setPlanSize() {

    let plan = $ID('plan');

    if (plan) {
        let resultHeight = $ID('result').clientHeight;
        let resultWidth = $ID('result').clientWidth;
        let smallerSide = resultWidth <= resultHeight ? resultWidth : resultHeight;
            smallerSide = smallerSide * 0.95;
        let roomLength = $ID('room-length').innerText.replace('cm', '');
        let roomWidth = $ID('room-width').innerText.replace('cm', '');

        plan.style.width = smallerSide + 'px';
        plan.style.height = smallerSide / (roomLength / roomWidth) + 'px';
    }
}

function scrollToPlan() {
    if ($ID('plan')) {
        document.getElementById('plan').scrollIntoView();
    }
}

function initNavbarListener() {
    let navBar = document.querySelector('.navbar-collapse');
    let navBarLinks = $QSA('.navbar-collapse a');
    navBarLinks.forEach(link => {
        link.addEventListener('click', function() {
            navBar.classList.toggle('show');
        })
    })
}

function initShowButtons() {
    $ID('toggleNumbers').addEventListener('click', function() {
        let numbers = $QSA('.number');
        numbers.forEach(number => {
            number.classList.toggle('d-none')
        })
    })

    $ID('toggleLengths').addEventListener('click', function() {
        let numbers = $QSA('.measure');
        numbers.forEach(number => {
            number.classList.toggle('d-none')
        })
    })
}

docReady(function () {
    scrollToPlan();
    addTileTypeSelectionBehavior();
    addPrintListener();
    setPlanSize();
    initNavbarListener();
    initShowButtons();

    let tiles = $QSA('.tile');
    tiles.forEach(tile => {
        tile.addEventListener('mouseover', addMouseOver);
        tile.addEventListener('mouseout', addMouseOut);
    })
});

function printResult() {
    let pdf = new jsPDF('p', 'pt', 'letter');
    pdf.addHTML($ID('result'), function () {
        pdf.save('planer.pdf');
    });
}