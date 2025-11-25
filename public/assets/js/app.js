// public/assets/js/app.js

document.addEventListener('DOMContentLoaded', () => {

    // ===================== MÁSCARA CPF =====================
    const cpfInputs = document.querySelectorAll('[data-mask="cpf"]');

    function aplicarMascaraCpf(input) {
        let value = input.value.replace(/\D/g, '');

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

        input.value = value;
    }

    cpfInputs.forEach(input => {
        // aplica enquanto digita
        input.addEventListener('input', () => aplicarMascaraCpf(input));

        // aplica ao carregar a tela (edição)
        if (input.value) {
            aplicarMascaraCpf(input);
        }
    });

    // ===================== AJAX ESTADOS → CIDADES =====================
    const estadoSelect = document.getElementById('select_estado');
    const cidadeSelect = document.getElementById('select_cidade');

    if (estadoSelect) {
        estadoSelect.addEventListener('change', async () => {

            const bro_id = estadoSelect.value;

            cidadeSelect.innerHTML = '<option value="">-- Selecione --</option>';

            if (!bro_id) return;

            try {
                const response = await fetch(`?action=cidadesPorEstado&bro_id=${bro_id}`);
                const cidades = await response.json();

                cidades.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.bre_id;
                    opt.textContent = c.bre_nome;
                    cidadeSelect.appendChild(opt);
                });

            } catch (e) {
                console.error("Erro ao carregar cidades:", e);
            }

        });
    }

    // ===================== VALIDAÇÃO CPF VIA AJAX NO FOCUSOUT =====================
    const cpfInput = document.querySelector('input[name="con_cpf"]');
    const cpfFeedback = document.getElementById('cpf_feedback');

    if (cpfInput && cpfFeedback) {
        cpfInput.addEventListener('blur', async () => {
            const cpf = cpfInput.value.trim();

            if (!cpf) {
                cpfInput.classList.remove('is-valid', 'is-invalid');
                cpfFeedback.textContent = '';
                cpfFeedback.classList.remove('text-danger', 'text-success');
                return;
            }

            const conIdInput = document.querySelector('input[name="con_id"]');
            const con_id = conIdInput ? conIdInput.value : '';

            try {
                const response = await fetch('?action=verificarCpf', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                    },
                    body: new URLSearchParams({
                        cpf: cpf,
                        con_id: con_id
                    })
                });

                const data = await response.json();

                cpfInput.classList.remove('is-valid', 'is-invalid');
                cpfFeedback.classList.remove('text-danger', 'text-success');

                if (data.success) {
                    cpfInput.classList.add('is-valid');
                    cpfFeedback.textContent = data.message || '';
                    cpfFeedback.classList.add('text-success');
                } else {
                    cpfInput.classList.add('is-invalid');
                    cpfFeedback.textContent = data.message || 'CPF inválido ou já utilizado.';
                    cpfFeedback.classList.add('text-danger');
                }

            } catch (e) {
                console.error('Erro ao verificar CPF:', e);
                cpfInput.classList.remove('is-valid', 'is-invalid');
                cpfFeedback.textContent = '';
                cpfFeedback.classList.remove('text-danger', 'text-success');
            }
        });
    }

    // ===================== MÁSCARA TELEFONE + VALIDAÇÃO VISUAL =====================
    const telInputs = document.querySelectorAll('[data-mask="telefone"]');
    const telFeedback = document.getElementById('telefone_feedback');

    function aplicarMascaraTelefone(input) {
        let value = input.value.replace(/\D/g, '');

        // Limita a 11 dígitos (2 DDD + 9 número)
        if (value.length > 11) {
            value = value.slice(0, 11);
        }

        if (value.length <= 10) {
            // Fixo: (99) 9999-9999
            value = value.replace(/(\d{0,2})(\d{0,4})(\d{0,4})/, function (_, ddd, p1, p2) {
                let result = '';

                if (ddd) result = `(${ddd}`;
                if (ddd && ddd.length === 2) result += ') ';

                if (p1) result += p1;
                if (p2) result += '-' + p2;

                return result;
            });
        } else {
            // Celular: (99) 99999-9999
            value = value.replace(/(\d{0,2})(\d{0,5})(\d{0,4})/, function (_, ddd, p1, p2) {
                let result = '';

                if (ddd) result = `(${ddd}`;
                if (ddd && ddd.length === 2) result += ') ';

                if (p1) result += p1;
                if (p2) result += '-' + p2;

                return result;
            });
        }

        input.value = value;
    }

    telInputs.forEach(input => {
        // aplica máscara enquanto digita
        input.addEventListener('input', () => aplicarMascaraTelefone(input));

        // aplica máscara no carregamento (edição)
        if (input.value) {
            aplicarMascaraTelefone(input);
        }

        // valida visualmente no blur
        input.addEventListener('blur', () => {
            if (!telFeedback) return;

            const digits = input.value.replace(/\D/g, '');
            input.classList.remove('is-valid', 'is-invalid');
            telFeedback.classList.remove('text-danger', 'text-success');
            telFeedback.textContent = '';

            if (!digits) {
                return;
            }

            if (digits.length !== 10 && digits.length !== 11) {
                input.classList.add('is-invalid');
                telFeedback.classList.add('text-danger');
                telFeedback.textContent = 'Telefone inválido. Informe DDD + número (10 ou 11 dígitos).';
            } else {
                input.classList.add('is-valid');
                telFeedback.classList.add('text-success');
                telFeedback.textContent = 'Telefone em formato válido.';
            }
        });
    });

});
