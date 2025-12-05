import { Validate } from "./Validate.js";
import { Requests } from "./Requests.js";
const Salvar = document.getElementById('salvar');

Salvar.addEventListener('click', async () => {
    Validate.setform('form').Validate();
    const response = Requests.setform('form').Post('/cliente/insert');
    console.log(response);
});