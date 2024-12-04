(()=>{"use strict";const e=window.React,t=window.wp.blocks,o=window.wp.i18n,n=window.wp.blockEditor,l=window.wp.components,r=(...e)=>e.filter(((e,t,o)=>Boolean(e)&&""!==e.trim()&&o.indexOf(e)===t)).join(" ").trim();var a={xmlns:"http://www.w3.org/2000/svg",width:24,height:24,viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:2,strokeLinecap:"round",strokeLinejoin:"round"};const c=(0,e.forwardRef)((({color:t="currentColor",size:o=24,strokeWidth:n=2,absoluteStrokeWidth:l,className:c="",children:i,iconNode:s,...d},m)=>(0,e.createElement)("svg",{ref:m,...a,width:o,height:o,stroke:t,strokeWidth:l?24*Number(n)/Number(o):n,className:r("lucide",c),...d},[...s.map((([t,o])=>(0,e.createElement)(t,o))),...Array.isArray(i)?i:[i]]))),i=((t,o)=>{const n=(0,e.forwardRef)((({className:n,...l},a)=>{return(0,e.createElement)(c,{ref:a,iconNode:o,className:r(`lucide-${i=t,i.replace(/([a-z0-9])([A-Z])/g,"$1-$2").toLowerCase()}`,n),...l});var i}));return n.displayName=`${t}`,n})("QrCode",[["rect",{width:"5",height:"5",x:"3",y:"3",rx:"1",key:"1tu5fj"}],["rect",{width:"5",height:"5",x:"16",y:"3",rx:"1",key:"1v8r4q"}],["rect",{width:"5",height:"5",x:"3",y:"16",rx:"1",key:"1x03jg"}],["path",{d:"M21 16h-3a2 2 0 0 0-2 2v3",key:"177gqh"}],["path",{d:"M21 21v.01",key:"ents32"}],["path",{d:"M12 7v3a2 2 0 0 1-2 2H7",key:"8crl2c"}],["path",{d:"M3 12h.01",key:"nlz23k"}],["path",{d:"M12 3h.01",key:"n36tog"}],["path",{d:"M12 16v.01",key:"133mhm"}],["path",{d:"M16 12h1",key:"1slzba"}],["path",{d:"M21 12v.01",key:"1lwtk9"}],["path",{d:"M12 21v-1",key:"1880an"}]]);(0,t.registerBlockType)("rena-plugin/qr-code",{edit:function({attributes:t,setAttributes:r}){const{content:a,buttonText:c,showIcon:s,bgColor:d,fgColor:m,size:u,buttonStyle:g}=t,h=(0,n.useBlockProps)();return(0,e.createElement)("div",{...h},(0,e.createElement)(n.InspectorControls,null,(0,e.createElement)(l.PanelBody,{title:(0,o.__)("QR Code Settings","rena-plugin")},(0,e.createElement)(l.TextControl,{label:(0,o.__)("Content","rena-plugin"),value:a,onChange:e=>r({content:e}),help:(0,o.__)("Enter text or URL for QR code","rena-plugin")}),(0,e.createElement)(l.TextControl,{label:(0,o.__)("Button Text","rena-plugin"),value:c,onChange:e=>r({buttonText:e})}),(0,e.createElement)(l.ToggleControl,{label:(0,o.__)("Show Icon","rena-plugin"),checked:s,onChange:e=>r({showIcon:e})}),(0,e.createElement)(l.RangeControl,{label:(0,o.__)("QR Code Size","rena-plugin"),value:u,onChange:e=>r({size:e}),min:128,max:512,step:32})),(0,e.createElement)(l.PanelBody,{title:(0,o.__)("Colors","rena-plugin")},(0,e.createElement)("div",{className:"qr-color-settings"},(0,e.createElement)("label",null,(0,o.__)("Background Color","rena-plugin")),(0,e.createElement)(l.ColorPicker,{color:d,onChangeComplete:e=>r({bgColor:e.hex}),disableAlpha:!0}),(0,e.createElement)("label",null,(0,o.__)("QR Code Color","rena-plugin")),(0,e.createElement)(l.ColorPicker,{color:m,onChangeComplete:e=>r({fgColor:e.hex}),disableAlpha:!0}))),(0,e.createElement)(l.PanelBody,{title:(0,o.__)("Button Style","rena-plugin")},(0,e.createElement)("div",{className:"button-style-settings"},(0,e.createElement)("label",null,(0,o.__)("Button Color","rena-plugin")),(0,e.createElement)(l.ColorPicker,{color:g.backgroundColor,onChangeComplete:e=>r({buttonStyle:{...g,backgroundColor:e.hex}}),disableAlpha:!0}),(0,e.createElement)("label",null,(0,o.__)("Button Text Color","rena-plugin")),(0,e.createElement)(l.ColorPicker,{color:g.textColor,onChangeComplete:e=>r({buttonStyle:{...g,textColor:e.hex}}),disableAlpha:!0})))),(0,e.createElement)("div",{className:"wp-block-rena-qr-code"},(0,e.createElement)("div",{className:"qr-content"},(0,e.createElement)(l.TextControl,{placeholder:(0,o.__)("Enter text or URL for QR code","rena-plugin"),value:a,onChange:e=>r({content:e})})),(0,e.createElement)(l.Button,{className:"qr-generate-button",style:{backgroundColor:g.backgroundColor,color:g.textColor,padding:g.padding},disabled:!0},s&&(0,e.createElement)(i,{className:"qr-icon",size:16}),(0,e.createElement)("span",null,c)),a&&(0,e.createElement)("div",{className:"qr-preview"},(0,e.createElement)("img",{src:`/wp-json/rena-plugin/v1/qr-code?content=${encodeURIComponent(a)}&size=${u}&bgcolor=${encodeURIComponent(d)}&color=${encodeURIComponent(m)}`,alt:"QR Code Preview",width:u,height:u}))))},save:function({attributes:t}){const{content:o,buttonText:l,showIcon:r,buttonStyle:a}=t,c=n.useBlockProps.save();return(0,e.createElement)("div",{...c},(0,e.createElement)("div",{className:"wp-block-rena-qr-code"},(0,e.createElement)("div",{className:"qr-content"},o),(0,e.createElement)("button",{className:"qr-generate-button","data-content":o,style:{backgroundColor:a.backgroundColor,color:a.textColor,padding:a.padding}},r&&(0,e.createElement)(i,{className:"qr-icon",size:16}),(0,e.createElement)("span",null,l))))}})})();