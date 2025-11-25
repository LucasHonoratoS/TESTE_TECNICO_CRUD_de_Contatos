$(function () {
    const $cpfInputs = $('[data-mask="cpf"]');

    function aplicarMascaraCpf($input) {
        let value = $input.val().replace(/\D/g, '');

        if (value.length > 11) {
            value = value.slice(0, 11);
        }

        if (value.length > 9) {
            value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
        } else if (value.length > 6) {
            value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
        } else if (value.length > 3) {
            value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
        }

        $input.val(value);
    }

    $cpfInputs.each(function () {
        const $input = $(this);

        $input.on('input', function () {
            aplicarMascaraCpf($input);
        });

        if ($input.val()) {
            aplicarMascaraCpf($input);
        }
    });

    const $estadoSelect = $('#select_estado');
    const $cidadeSelect = $('#select_cidade');

    if ($estadoSelect.length) {
        $estadoSelect.on('change', function () {
            const bro_id = $(this).val();

            $cidadeSelect.html('<option value="">-- Selecione --</option>');

            if (!bro_id) return;

            $.getJSON('?action=cidadesPorEstado&bro_id=' + encodeURIComponent(bro_id))
                .done(function (cidades) {
                    $.each(cidades, function (_, c) {
                        $('<option>', { value: c.bre_id, text: c.bre_nome }).appendTo($cidadeSelect);
                    });
                })
                .fail(function (e) {
                    console.error('Erro ao carregar cidades:', e);
                });
        });
    }

    const $cpfInput = $('input[name="con_cpf"]');
    const $cpfFeedback = $('#cpf_feedback');

    if ($cpfInput.length && $cpfFeedback.length) {
        $cpfInput.on('blur', function () {
            const cpf = $.trim($cpfInput.val());

            if (!cpf) {
                $cpfInput.removeClass('is-valid is-invalid');
                $cpfFeedback.text('').removeClass('text-danger text-success');
                return;
            }

            const con_id = $('input[name="con_id"]').val() || '';

            $.ajax({
                url: '?action=verificarCpf',
                method: 'POST',
                data: { cpf: cpf, con_id: con_id },
                dataType: 'json'
            })
                .done(function (data) {
                    $cpfInput.removeClass('is-valid is-invalid');
                    $cpfFeedback.removeClass('text-danger text-success');

                    if (data.success) {
                        $cpfInput.addClass('is-valid');
                        $cpfFeedback.text(data.message || '').addClass('text-success');
                    } else {
                        $cpfInput.addClass('is-invalid');
                        $cpfFeedback
                            .text(data.message || 'CPF inválido ou já utilizado.')
                            .addClass('text-danger');
                    }
                })
                .fail(function (e) {
                    console.error('Erro ao verificar CPF:', e);
                    $cpfInput.removeClass('is-valid is-invalid');
                    $cpfFeedback.text('').removeClass('text-danger text-success');
                });
        });
    }

    const $telInputs = $('[data-mask="telefone"]');
    const $telFeedback = $('#telefone_feedback');

    function aplicarMascaraTelefone($input) {
        let value = $input.val().replace(/\D/g, '');

        if (value.length > 11) {
            value = value.slice(0, 11);
        }

        if (value.length <= 10) {
            value = value.replace(/(\d{0,2})(\d{0,4})(\d{0,4})/, function (_, ddd, p1, p2) {
                let result = '';
                if (ddd) result = '(' + ddd;
                if (ddd && ddd.length === 2) result += ') ';
                if (p1) result += p1;
                if (p2) result += '-' + p2;
                return result;
            });
        } else {
            value = value.replace(/(\d{0,2})(\d{0,5})(\d{0,4})/, function (_, ddd, p1, p2) {
                let result = '';
                if (ddd) result = '(' + ddd;
                if (ddd && ddd.length === 2) result += ') ';
                if (p1) result += p1;
                if (p2) result += '-' + p2;
                return result;
            });
        }

        $input.val(value);
    }

    $telInputs.each(function () {
        const $input = $(this);

        $input.on('input', function () {
            aplicarMascaraTelefone($input);
        });

        if ($input.val()) {
            aplicarMascaraTelefone($input);
        }

        $input.on('blur', function () {
            if (!$telFeedback.length) return;

            const digits = $input.val().replace(/\D/g, '');
            $input.removeClass('is-valid is-invalid');
            $telFeedback.removeClass('text-danger text-success').text('');

            if (!digits) {
                return;
            }

            if (digits.length !== 10 && digits.length !== 11) {
                $input.addClass('is-invalid');
                $telFeedback
                    .addClass('text-danger')
                    .text('Telefone inválido. Informe DDD + número (10 ou 11 dígitos).');
            } else {
                $input.addClass('is-valid');
                $telFeedback
                    .addClass('text-success')
                    .text('Telefone em formato válido.');
            }
        });
    });
});
