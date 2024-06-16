
      var linenumbers_{ $id } = document.getElementById('line-numbers-{ $id }');
      var editor_{ $id } = document.getElementById('codeblock-{ $id }');

  function getLineNumber_{ $id }() {
      var textLines = editor_{ $id }.value.substr(0, editor_{ $id }.selectionStart).split("\n");
      var currentLineNumber = textLines.length;
      var currentColumnIndex = textLines[textLines.length-1].length;
      return currentLineNumber;
  }

  function lineNumbers_{ $id }() {


      var totallines = cutLines(editor_{ $id }.value), linesize;

      linenumbers_{ $id }.innerHTML = '';
      for (var i = 1; i <= totallines.length; i++) {
          var num = document.createElement('p');
          num.innerHTML = i;
          linenumbers_{ $id }.appendChild(num);

          linesize = getTotalLineSize(getWidth(editor_{ $id }), totallines[(i - 1)], {'fontSize' : getFontSize(editor_{ $id })});
          if (linesize > 1) {
              num.style.height = (linesize * getLineHeight(editor_{ $id })) + 'px';
          }
      }

      linesize = getTotalLineSize(getWidth(editor_{ $id }), totallines[(getLineNumber_{ $id }() - 1)], {'fontSize' : getFontSize(editor_{ $id })});
      if (linesize > 1) {
          linenumbers_{ $id }.childNodes[(getLineNumber_{ $id }() - 1)].style.height = (linesize * getLineHeight(editor_{ $id })) + 'px';
      }

      editor_{ $id }.style.height = editor_{ $id }.scrollHeight;
      linenumbers_{ $id }.style.height = editor_{ $id }.scrollHeight;
  }
