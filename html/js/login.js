import { Validate } from "./Validate.js";
import { Requests } from "./Requests.js";

const preCadastro = document.getElementById('preCadastro');

$('#cpf').inputmask({ "mask": ["999.999.999-99"] });
$('#celular').inputmask({ "mask": ["(99) 99999-9999"] });
$('#whatsapp').inputmask({ "mask": ["(99) 99999-9999"] });

preCadastro.addEventListener('click', async () => {
    try {
        const response = await Requests.setform('form').Post('/login/PreCadastro');
        if (!response.status) {
            Swal.fire({
                title: 'Atenção!',
                text: response.msg,
                icon: "error",
                timer: 3000,
                showConfirmButton: false
            });
            return;
        }
        Swal.fire({
            title: 'Sucesso!',
            text: response.msg,
            icon: "success",
            timer: 3000,
            showConfirmButton: false
        });
        $('#pre-cadastro').modal('hide');
    } catch (error) {
        console.error('Erro ao processar o pré-cadastro:', error);
        Swal.fire({
            title: 'Erro!',
            text: 'Ocorreu um erro ao processar o pré-cadastro. Por favor, tente novamente mais tarde.',
            icon: "error",
            timer: 3000,
            showConfirmButton: false
        });
    }

});