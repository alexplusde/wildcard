function data_copy(event) {
    // Holt das aktuelle Element, auf das geklickt wurde
    var element = event.currentTarget;
    // Fügt die Klasse "copied" zum aktuellen Element hinzu
    element.classList.add('copied');
    // Sucht das icon-Element im aktuellen Element
    var iconElement = element.querySelector('i');
    // Entfernt die Klasse "fa-clone" vom i-Element
    iconElement.classList.remove('fa-clone');
    // Fügt die Klasse "fa-check" zum i-Element hinzu
    iconElement.classList.add('fa-check');
    // Kopiert den Wert des data-wildcard-copy Attributs in die Zwischenablage
    navigator.clipboard.writeText(element.getAttribute('data-wildcard-copy'));
};

$(document).on("rex:ready", function(wildcard, container) {

    // Fügt den Click-Event-Listener zu allen div-Elementen mit dem data-wildcard-copy Attribut hinzu
    document.querySelectorAll('div[data-wildcard-copy]').forEach(function(el) {
        el.addEventListener('click', data_copy)
    })

    // Warte auf QuickNavigation -  Event an das Formular erst nach 2 Sekunden
    setTimeout(function() {

        // Fügt den Submit-Event-Listener zum Formular mit der ID "wildcard_search" hinzu
        $('#wildcard_search').on('submit', function(e) {
            // Verhindert das Absenden des Formulars
            e.preventDefault();

            // Sendet eine AJAX-Anfrage
            $.ajax({
                url: '/', // URL, an die die Anfrage gesendet wird
                type: 'GET', // Methode der Anfrage
                data: {
                    "rex-api-call": 'wildcard_search', // Daten, die an den Server gesendet werden
                    "q": $('input[name="q"]').val() // Wert des Eingabefelds
                },
                success: function(response) {
                    // Erstellt ein leeres table-Element und fügt Klassen hinzu
                    var table = $('<table></table>');
                    table.addClass('table table-striped table-hover');
                    // Erstellt ein leeres tbody-Element
                    var tbody = $('<tbody></tbody>');

                    // Durchläuft jedes Element im Antwortobjekt
                    $.each(response, function(key, value) {
                        // Erstellt ein neues tr-Element und fügt das gewünschte HTML hinzu
                        var tr = $('<tr></tr>');
                        var th = $('<th></th>');
                        var div = $('<div></div>').attr('data-wildcard-copy', key).attr('role', 'button');
                        var i = $('<i></i>').addClass('fa fa-clone').attr('aria-hidden', 'true');
                        var code = $('<code></code>').text(key);
                        div.append(i);
                        div.append(code);
                        th.append(div);
                        var td = $('<td></td>').text("⫸ " + value['de_DE']);
                        tr.append(th);
                        tr.append(td);

                        // Fügt das tr-Element zum tbody-Element hinzu
                        tbody.append(tr);
                        // Fügt das tbody-Element zum table-Element hinzu
                        table.append(tbody);
                    });

                    // Fügt das table-Element in das div mit der ID "wildcardSearchResults" ein
                    $('#wildcardSearchResults').html(table);

                    // Fügt den Click-Event-Listener zu allen neuen div-Elementen mit dem data-wildcard-copy Attribut hinzu
                    document.querySelectorAll('div[data-wildcard-copy]').forEach(function(el) {
                        el.addEventListener('click', data_copy)
                    })

                },
                error: function() {
                    // Zeigt eine Fehlermeldung an, wenn ein Fehler auftritt
                    $('#wildcardSearchResults').html('Ein Fehler ist aufgetreten.');
                }
            });
        });

    }, 2000);

});
