import { useState } from "react";
import { remove } from "lodash";
function useFormData(defaultState) {
	const handleInputChange = (e) => {
		const { name, value, type, checked } = e.target;

		if (type === "radio") {
			setValues({ ...values, [name]: checked });
		} else if (type === "checkbox") {
			if (checked) {
				setValues({ ...values, [name]: [...values[name], value] });
			} else {
				setValues({
					...values,
					[name]: remove(values[name], (item) => {
						if (item === value) {
							return false;
						}
						return true;
					}),
				});
			}
		} else {
			setValues({ ...values, [name]: value });
		}
	};

	const handleChangeCheckboxNoMultiple = (e) => {
		const { name, value, checked } = e.target;
		if (checked) {
			setValues({ ...values, [name]: [value] });
		} else {
			setValues({
				...values,
				[name]: remove(values[name], (item) => {
					if (item === value) {
						return false;
					}
					return true;
				}),
			});
		}
	};

	const [values, setValues] = useState(defaultState);

	return [
		values,
		handleInputChange,
		handleChangeCheckboxNoMultiple,
		setValues,
	];
}

export default useFormData;
