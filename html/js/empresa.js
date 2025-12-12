import { Validate } from "./Validate.js";
import { Requests } from "./Requests.js";
const Salvar = document.getElementById('insert');
const Acao = document.getElementById('acao');


$('#cnpj').inputmask({ "mask": ["999.999.999-99", "99.999.999/9999-99"] });
$('#celular').inputmask({ "mask": ["(99) 99999-9999"] });
$('#whatsapp').inputmask({ "mask": ["(99) 99999-9999"] });

Salvar.addEventListener('click', async () => {
    Validate.SetForm('form').Valid();
    const response = await (Acao.value === 'inserir' ? Requests.SetForm('form').Post('/empresa/insert') : Requests.SetForm('form').Post('/empresa/update'));
    if (!response.status) {
        Swal.fire({
            icon: 'error',
            title: 'Por favor, preencha todos os campos obrigatórios.',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        return;
    }

    Swal.fire({
        title: "Cadastro realizado com sucesso!",
        icon: "success",
        draggable: true
    });

});