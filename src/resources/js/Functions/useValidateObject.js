// https://stackoverflow.com/questions/38616612/javascript-elegant-way-to-check-object-has-required-properties

//const schema = {
//    name: value => /^([A-Z][a-z\-]* )+[A-Z][a-z\-]*( \w+\.?)?$/.test(value),
//    age: value => parseInt(value) === Number(value) && value >= 18,
//    phone: value => /^(\+?\d{1,2}-)?\d{3}-\d{3}-\d{4}$/.test(value)
//};

export function useValidateObject(object, schema) {

    const validate = (object, schema) => Object
        .entries(schema)
        .map(([key, validate]) => [
            key,
            !validate.required || (key in object),
            validate(object[key])
        ])
        .filter(([_, ...tests]) => !tests.every(Boolean))
        .map(([key, invalid]) => new Error(`${key} is ${invalid ? 'invalid' : 'required'}.`));

    const errors = validate(object, schema);

    if (errors.length > 0) {
        for (const { message } of errors) {
            console.log(message);
        }
        return false
    }

    return true
}