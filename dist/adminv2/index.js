(()=>{"use strict";var e={n:t=>{var a=t&&t.__esModule?()=>t.default:()=>t;return e.d(a,{a}),a},d:(t,a)=>{for(var n in a)e.o(a,n)&&!e.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:a[n]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t)};window.React;const t=window.wp.components,a=window.wp.element,n=window.wp.i18n,i=window.wp.apiFetch;var s=e.n(i);const o=async e=>{try{return await s()({path:"imageseo/v1/optimize-image/",method:"POST",data:e})}catch(e){console.error("Error calling the api:",e)}},r=async e=>{try{return await s()({path:"imageseo/v1/save-property/",method:"POST",data:e})}catch(e){console.error("Error calling the api:",e)}},c=new class{constructor(){this.listeners={}}subscribe(e,t){this.listeners[e]||(this.listeners[e]=[]),this.listeners[e].push(t)}publish(e,t){this.listeners[e]&&this.listeners[e].forEach((e=>e(t)))}unsubscribe(e,t){this.listeners[e]&&(this.listeners[e]=this.listeners[e].filter((e=>e!==t)))}};function l({attachmentId:e,alt:i}){const[s,l]=(0,a.useState)(i),[m,u]=(0,a.useState)(!1),[d,p]=(0,a.useState)(!1),[h,g]=(0,a.useState)(null),[b,_]=(0,a.useState)("");return React.createElement(t.Flex,{direction:"column"},React.createElement(t.FlexItem,null,React.createElement(t.TextControl,{label:(0,n.__)("Alt","imageseo"),value:s||"",onChange:l})),React.createElement(t.FlexItem,null,React.createElement(t.ButtonGroup,null,React.createElement(t.Button,{style:{margin:"0 6px"},onClick:async()=>{m||(u(!0),g(!1),_("save"),r({id:e,action:"saveAlt",value:s}).then((e=>{u(!1),e?.error||(l(e.altText||""),p(!0),g(!0),c.publish("snackbar",{content:(0,n.__)("Alt saved","imageseo"),status:"info"}))})).catch((e=>{console.error(e),u(!1),p(!0),g(!1),c.publish("snackbar",{content:(0,n.__)("Something went wrong!","imageseo"),status:"error"})})))},isPrimary:!0,isBusy:m},"save"===b&&d&&h?(0,n.__)("Saved","imageseo"):(0,n.__)("Save","imageseo")),React.createElement(t.Button,{variant:"tertiary",onClick:async()=>{m||(u(!0),g(!1),_("optimize"),o({id:e,action:"optimizeAlt"}).then((e=>{u(!1),e?.error||(l(e.altText||""),p(!0),g(!0),c.publish("snackbar",{content:(0,n.__)("Alt optimized","imageseo"),status:"info"}))})).catch((e=>{c.publish("snackbar",{content:(0,n.__)("Something went wrong!","imageseo"),status:"error"}),console.error(e),u(!1),p(!0),g(!1)})))},isBusy:m},"optimize"===b&&d&&h?(0,n.__)("Optimized","imageseo"):(0,n.__)("Optimize","imageseo")))))}function m({attachmentId:e,filename:i}){const[s,l]=(0,a.useState)(i),[m,u]=(0,a.useState)(!1),[d,p]=(0,a.useState)(!1),[h,g]=(0,a.useState)(null),[b,_]=(0,a.useState)("");return React.createElement(React.Fragment,null,React.createElement(t.Flex,{direction:"column"},React.createElement(t.FlexItem,null,React.createElement(t.TextControl,{label:(0,n.__)("Filename","imageseo"),value:s||"",onChange:l})),React.createElement(t.FlexItem,null,React.createElement(t.ButtonGroup,null,React.createElement(t.Button,{style:{margin:"0 6px"},onClick:async()=>{m||(u(!0),g(!1),_("save"),r({id:e,action:"saveFilename",value:s}).then((e=>{u(!1),e?.error||(p(!0),g(!0),l(e.filename||""),c.publish("snackbar",{content:(0,n.__)("Filename saved","imageseo"),status:"info"}))})).catch((e=>{console.error(e),p(!0),g(!1),u(!1),c.publish("snackbar",{content:(0,n.__)("Something went wrong!","imageseo"),status:"error"})})))},isPrimary:!0,isBusy:m},"save"===b&&d&&h?(0,n.__)("Saved","imageseo"):(0,n.__)("Save","imageseo")),React.createElement(t.Button,{variant:"tertiary",onClick:async()=>{m||(_("optimize"),u(!0),g(!1),o({id:e,action:"optimizeFilename"}).then((e=>{u(!1),e?.error||(l(e.filename||""),p(!0),g(!0),c.publish("snackbar",{content:(0,n.__)("Filename optimized","imageseo"),status:"info"}))})).catch((e=>{console.error(e),u(!1),g(!1),p(!0),c.publish("snackbar",{content:(0,n.__)("Something went wrong!","imageseo"),status:"error"})})))},isBusy:m},"optimize"===b&&d&&h?(0,n.__)("Optimized","imageseo"):(0,n.__)("Optimize","imageseo"))))))}function u(){const[e,n]=(0,a.useState)([]),i=(0,a.useCallback)((e=>{const t={id:(new Date).getTime(),...e,content:e?.content||"",politeness:e?.politeness||"polite",actions:e?.actions||[],explicitDismiss:e?.explicitDismiss||!1};n((e=>[...e,t]))}),[]),s=(0,a.useCallback)((e=>{n((t=>t.filter((t=>t.id!==e))))}),[]);return(0,a.useEffect)((()=>(console.log("Snackbar mounted"),c.subscribe("snackbar",i),()=>{c.unsubscribe("snackbar",i)})),[i]),React.createElement(t.SnackbarList,{notices:e,onRemove:s})}document.addEventListener("DOMContentLoaded",(()=>{const e=document.querySelectorAll(".media-column-imageseo-alt"),t=document.querySelectorAll(".media-column-imageseo-filename");e.forEach((e=>{const t=(0,a.createRoot)(e),n=e.getAttribute("data-id"),i=e.getAttribute("data-alt");t.render(React.createElement(l,{attachmentId:n,alt:i}))})),t.forEach((e=>{const t=(0,a.createRoot)(e),n=e.getAttribute("data-id"),i=e.getAttribute("data-filename");t.render(React.createElement(m,{attachmentId:n,filename:i}))}));const n=document.getElementById("wpbody-content"),i=document.createElement("div"),s=document.createElement("div");s.setAttribute("id","imageseo-snackbar-wrapper"),i.setAttribute("id","imageseo-snackbar-root"),s.style.position="fixed",s.style.left="10px",s.style.right="10px",s.style.bottom="10px",s.style.zIndex="1000",s.style.textAlign="center",s.style.width="100%",s.style.height="50px",s.style.pointerEvents="none",n.prepend(s),s.appendChild(i),(0,a.createRoot)(document.getElementById("imageseo-snackbar-root")).render(React.createElement(u,null))}))})();