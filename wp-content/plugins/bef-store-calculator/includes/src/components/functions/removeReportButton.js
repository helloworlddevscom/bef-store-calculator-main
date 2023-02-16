export function removeReportButton(formId) {
  const getSubmitButton = document.getElementById(`gform_submit_${formId}`);

  const firstStep = document.getElementById(`gf_step_${formId}_1`);
  const firstStepClasses = firstStep.classList.contains('gf_step_active');

  if (firstStepClasses) {
    getSubmitButton.style.display = "none";
  } else {
    getSubmitButton.style.display = "block";
  }
}