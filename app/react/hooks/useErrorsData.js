import { useState } from 'react'
import { countBy, isNull } from 'lodash'

function useErrorsData(initState = {}) {
    const [errors, setErrors] = useState(initState)
    const setError = (key, value) => {
        setErrors({ ...errors, [key]: value })
    }

    const allIsValid = (items = null) => {
        const countErrors = countBy(isNull(items) ? errors : items, {
            error: true
        })
        return (countErrors.true || 0) === 0
    }

    return [errors, { setErrors, setError, allIsValid }]
}

export default useErrorsData
