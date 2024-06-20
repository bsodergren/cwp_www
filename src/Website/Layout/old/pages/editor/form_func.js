"use scrict";

function getWidth(elem) {
    return (
        elem.scrollWidth -
        (parseFloat(
            window
                .getComputedStyle(elem, null)
                .getPropertyValue("padding-left")
        ) +
            parseFloat(
                window
                    .getComputedStyle(elem, null)
                    .getPropertyValue("padding-right")
            ))
    );
}

function getFontSize(elem) {
    return parseFloat(
        window.getComputedStyle(elem, null).getPropertyValue("font-size")
    );
}

function cutLines(lines) {
    return lines.split(/\r?\n/);
}

function getLineHeight(elem) {
    var computedStyle = window.getComputedStyle(elem);
    var lineHeight = computedStyle.getPropertyValue("line-height");
    var lineheight;

    if (lineHeight === "normal") {
        var fontSize = computedStyle.getPropertyValue("font-size");
        lineheight = parseFloat(fontSize) * 1.2;
    } else {
        lineheight = parseFloat(lineHeight);
    }

    return lineheight;
}

function getTotalLineSize(size, line, options) {
    if (typeof options === "object") options = {};
    var p = document.createElement("span");
    p.style.setProperty("white-space", "pre");
    p.style.display = "inline-block";
    if (typeof options.fontSize !== "undefined")
        p.style.fontSize = options.fontSize;
    p.innerHTML = line;
    document.body.appendChild(p);
    var result = p.scrollWidth / size;
    p.remove();
    return Math.ceil(result);
}