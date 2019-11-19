import React from "react";
import { ErrorMessage, Field, Formik } from "formik";
import Form from "reactstrap/es/Form";

const SingleField = () => {
    return (
        <Formik
            initialValues={{
                customer_name: ""
            }}
            onSubmit={values => {
                alert(JSON.stringify(values, null, 2));
            }}
        >
            <Form>
                <label htmlFor="customer_name">Customer Name</label>
                <Field id="customer_name" name="customer_name" type="text" />
                <ErrorMessage name="customer_name" />
                <button type="submit">Submit</button>
            </Form>
        </Formik>
    );
};
