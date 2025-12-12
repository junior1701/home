import { Validate } from "./Validate.js";
import { Requests } from "./Requests.js";
const Salvar = document.getElementById('insert');
const Acao = document.getElementById('acao');

$('#cpf').inputmask({ "mask": ["999.999.999-99", "99.999.999/9999-99"] });
$('#celular').inputmask({ "mask": ["(99) 9999-0000"] });
$('#whatsapp').inputmask({ "mask": ["(99) 0000-0000"] });

Salvar.addEventListener('click', async () => {
    Validate.SetForm('form').Valid();
    const response = await (Acao.value === 'inserir' ? Requests.SetForm('form').Post('/usuario/insert') : Requests.SetForm('form').Post('/usuario/update'));
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

//
// const response = Requests.SetForm('form').Post('/usuario/insert');
//
// if (!response.status) {
//     Swal.fire({
//         icon: 'error',
//         title: response.status,
//         showConfirmButton: false,
//         timer: 3000,
//         timerProgressBar: true,
//         didOpen: () => {
//             Swal.showLoading();
//         }
//     });
//     return;
// }
//

