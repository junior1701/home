import { Validate } from "./Validate.js";

$('#cpfCnpj').inputmask({ "mask": ["999.999.999-99", "99.999.999/9999-99"] });
$('#telefone').inputmask({ "mask": ["(99) 9999-9999", "(99) 99999-9999"] });



const InsertButton = document.getElementById('insert');

InsertButton.addEventListener('click', async () => {
    const IsValid = Validate
        .SetForm('form')
        .Valid();

});