import { useState } from 'react'
function useFormData(defaultState) {
    const handleInputChange = e => {
        const { name, value, type, checked } = e.target

        if (type === 'checkbox' || type === 'radio') {
            setValues({ ...values, [name]: checked })
        } else {
            setValues({ ...values, [name]: value })
        }
    }

    const [values, setValues] = useState(defaultState)

    return [values, handleInputChange]
}

export default useFormData
