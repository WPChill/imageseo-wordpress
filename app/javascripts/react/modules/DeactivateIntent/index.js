import React, { useRef, useState, useEffect } from "react";
import GlobalStyle from "../../components/GlobalStyle";
import Modal from "../../ui/Modal";
import ModalOverlay from "../../ui/Modal/Overlay";
import ModalTitle from "../../ui/Modal/Title";
import ModalContent from "../../ui/Modal/Content";
import ModalIconClose from "../../ui/Modal/IconClose";
import useFormData from "../../hooks/useFormData";
import useOnClickOutside from "../../hooks/useOnClickOutside";
import Title from "../../ui/Title";
import Text from "../../ui/Text";
import Checkbox from "../../ui/Input/Checkbox";
import Textarea from "../../ui/Textarea";

const StepOne = ({ onClickClose, handleEndChurn = null }) => {
	const [values, handleInputChange] = useFormData({
		deactivate_temporary: false,
		bad_support: false,
		plugin_complicated: false,
		lack_feature: false,
		message: ""
	});

	const hrefDeactivate = document
		.querySelector(`.plugins [data-slug="imageseo"] .deactivate a`)
		.getAttribute("href");

	const handleSubmit = async e => {
		e.preventDefault();

		$.ajax({
			url: IMAGESEO_DATA.ADMIN_AJAX_URL,
			method: "POST",
			data: {
				action: "imageseo_deactivate_plugin",
				values
			},
			success: response => {
				document.querySelector("#imageseo-go-deactivate").click();
			},

			fail: () => {
				document.querySelector("#imageseo-go-deactivate").click();
			}
		});
	};

	return (
		<>
			<ModalTitle style={{ textAlign: "left" }}>
				<Title>{imageseo_i18n.modal_title}</Title>
			</ModalTitle>
			<ModalContent>
				<Checkbox
					name="deactivate_temporary"
					onChange={handleInputChange}
					checked={values.deactivate_temporary}
					style={{ marginTop: 0 }}
				>
					<Text>{imageseo_i18n.reasons.deactivate_temporary}</Text>
				</Checkbox>
				<Checkbox
					name="bad_support"
					onChange={handleInputChange}
					checked={values.bad_support}
				>
					<div>
						<Text>{imageseo_i18n.reasons.bad_support}</Text>
						{values.bad_support && (
							<Text bold className="mt-1">
								{imageseo_i18n.reasons.bad_support_helper}
							</Text>
						)}
					</div>
				</Checkbox>
				<Checkbox
					name="plugin_complicated"
					onChange={handleInputChange}
					checked={values.plugin_complicated}
				>
					<div>
						<Text>{imageseo_i18n.reasons.plugin_complicated}</Text>
						{values.plugin_complicated && (
							<Text bold className="mt-1">
								{
									imageseo_i18n.reasons
										.plugin_complicated_helper
								}
							</Text>
						)}
					</div>
				</Checkbox>
				<Checkbox
					name="lack_feature"
					onChange={handleInputChange}
					checked={values.lack_feature}
				>
					<div>
						<Text>{imageseo_i18n.reasons.lack_feature}</Text>
						{values.lack_feature && (
							<Text bold className="mt-1">
								{imageseo_i18n.reasons.lack_feature_helper}
							</Text>
						)}
					</div>
				</Checkbox>
				<Text className="mt-2 mb-2">Message :</Text>
				<Textarea
					name="message"
					onChange={handleInputChange}
					value={values.message}
				/>
				<div className="d-flex mt-4 align-items-center">
					<button
						className="button button-primary"
						onClick={handleSubmit}
					>
						{imageseo_i18n.button_submit}
					</button>
					<a
						href="#"
						style={{ marginLeft: 20 }}
						onClick={e => {
							e.preventDefault();
							onClickClose();
						}}
					>
						{imageseo_i18n.cancel}
					</a>
					<a
						id="imageseo-go-deactivate"
						className="button button-secondary"
						style={{ marginLeft: "auto" }}
						href={hrefDeactivate}
					>
						{imageseo_i18n.skip}
					</a>
				</div>
			</ModalContent>
		</>
	);
};

function DeactivateIntent() {
	const ref = useRef(null);
	const [isOpen, setIsOpen] = useState(false);
	const [step, setStep] = useState(1);

	const onClickClose = () => setIsOpen(false);

	useOnClickOutside(ref, () => setIsOpen(false));

	useEffect(() => {
		function openModal(e) {
			e.preventDefault();
			setIsOpen(true);
		}

		document
			.querySelector(`.plugins [data-slug="imageseo"] .deactivate`)
			.addEventListener("click", openModal);
		return () => {
			document
				.querySelector(`.plugins [data-slug="imageseo"] .deactivate`)
				.removeEventListener("click", openModal);
		};
	}, []);

	return (
		<>
			<GlobalStyle />
			{isOpen && (
				<ModalOverlay>
					<div ref={ref}>
						<Modal>
							<ModalIconClose onClick={onClickClose}>
								<img
									src={`${IMAGESEO_DATA.IMAGESEO_URL_DIST}/images/cross-grey.svg`}
								/>
							</ModalIconClose>
							{step === 1 && (
								<StepOne
									setStep={setStep}
									onClickClose={onClickClose}
								/>
							)}
						</Modal>
					</div>
				</ModalOverlay>
			)}
		</>
	);
}

export default DeactivateIntent;
